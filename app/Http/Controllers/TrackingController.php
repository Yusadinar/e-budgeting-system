<?php
// app/Http/Controllers/TrackingController.php

namespace App\Http\Controllers;

use App\Models\Ppbj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View
    {
        $user  = Auth::user();
        $search = $request->input('search');

        $query = Ppbj::with([
            'user.department',
            'latestProposalHarga.internalAgreement',
        ]);

        // Search filter logic
        $applySearch = function($q) use ($search) {
            if ($search) {
                $q->where(function($q2) use ($search) {
                    $q2->where('ppbj_number', 'like', "%{$search}%")
                       ->orWhereHas('latestProposalHarga', function($ph) use ($search) {
                           $ph->where('subject', 'like', "%{$search}%");
                       })
                       ->orWhereHas('user', function($u) use ($search) {
                           $u->where('name', 'like', "%{$search}%");
                       });
                });
            }
        };

        $applySearch($query);

        // Filter berdasarkan role
        if ($user->role === 'staff') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'ka_dept') {
            $query->whereHas('user', fn($q) => $q->where('dept_id', $user->dept_id));
        }

        $pendingApprovals = collect();

        // Cari dokumen yang butuh approval jika user adalah approver
        if ($user->canApprove()) {
            $role = $user->role;
            $deptId = $user->dept_id;

            // PPBJ Pending
            $pendingPpbjIds = \App\Models\Ppbj::where('status', 'In_Review')
                ->where(function($q) use ($user, $deptId) {
                    if ($user->isKaDept()) {
                        $q->orWhere(function($q1) use ($deptId) {
                            $q1->where('approval_step', 1)->whereHas('user', fn($q2) => $q2->where('dept_id', $deptId));
                        });
                    }
                    if ($user->isKaDiv()) {
                        $q->orWhere('approval_step', 2);
                    }
                    if ($user->isAccounting()) {
                        $q->orWhere('approval_step', 3);
                    }
                    if (!$user->isKaDept() && !$user->isKaDiv() && !$user->isAccounting()) {
                        $q->where('id', 0);
                    }
                })
                ->pluck('id');

            // PH Pending
            $pendingPhPpbjIds = \App\Models\ProposalHarga::where('status', 'In_Review')
                ->where(function($q) use ($user, $deptId) {
                    if ($user->isKaDept()) {
                        $q->orWhere(function($q1) use ($deptId) {
                            $q1->where('approval_step', 1)->whereHas('ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
                        });
                    }
                    if ($user->isKaDiv()) {
                        $q->orWhere('approval_step', 2);
                    }
                    if ($user->isAccounting()) {
                        $q->orWhere('approval_step', 3);
                    }
                    if (!$user->isKaDept() && !$user->isKaDiv() && !$user->isAccounting()) {
                        $q->where('id', 0);
                    }
                })
                ->pluck('ppbj_id');

            // IA Pending
            $pendingIaPpbjIds = \App\Models\InternalAgreement::where('status_ia', 'In_Review')
                ->where(function($q) use ($user, $deptId) {
                    if ($user->isKaDept()) {
                        $q->orWhere(function($q1) use ($deptId) {
                            $q1->where('approval_step', 1)->whereHas('proposalHarga.ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
                        });
                    }
                    if ($user->isKaDiv()) {
                        $q->orWhere('approval_step', 2);
                    }
                    if ($user->isKaDeptAcc()) {
                        $q->orWhere('approval_step', 3);
                    }
                    if ($user->isKaDivAcc()) {
                        $q->orWhere('approval_step', 4);
                    }
                    if ($user->isFinDir()) {
                        $q->orWhere('approval_step', 5);
                    }
                    if ($user->isManDir()) {
                        $q->orWhere('approval_step', 6);
                    }
                    if ($user->isPresDir()) {
                        $q->orWhere('approval_step', 7);
                    }
                    if (!$user->canApprove()) {
                        $q->where('id', 0);
                    }
                })
                ->with('proposalHarga') // Eager load
                ->get()
                ->pluck('proposalHarga.ppbj_id')
                ->filter();

            $allPendingPpbjIds = $pendingPpbjIds->merge($pendingPhPpbjIds)->merge($pendingIaPpbjIds)->unique();

            if ($allPendingPpbjIds->isNotEmpty()) {
                $pendingQuery = Ppbj::with([
                    'user.department',
                    'latestProposalHarga.internalAgreement',
                ])->whereIn('id', $allPendingPpbjIds);

                if (isset($applySearch)) {
                    $applySearch($pendingQuery);
                }

                $pendingApprovals = $pendingQuery->latest()->get();

                // Keluarkan dokumen yang masuk pending approval dari daftar pengajuan reguler agar tidak double
                $query->whereNotIn('id', $allPendingPpbjIds);
            }
        }

        $pengajuan = $query->latest()->paginate(10, ['*'], 'page')->withQueryString();

        // Pengajuan Orang Lain (seluruh perusahaan yang bukan di scope query utama)
        $otherQuery = Ppbj::with([
            'user.department',
            'latestProposalHarga.internalAgreement',
        ]);
        
        if ($user->role === 'staff') {
            $otherQuery->where('user_id', '!=', $user->id);
        } elseif ($user->role === 'ka_dept') {
            $otherQuery->whereHas('user', fn($q) => $q->where('dept_id', '!=', $user->dept_id));
        } else {
            // For roles that already see everything (like accounting, direksi), 
            // the main query already includes everything. So otherQuery can be empty,
            // or we just distinguish 'my own' vs 'others' for EVERYONE.
            // Let's make "Pengajuan" = "Ajuan Saya", "Other" = "Ajuan Orang Lain" for everyone?
            // User requested: "berikan section untuk bisa melihat ajuan orang lain juga"
            // For super roles, they already see all. To prevent duplication:
            $otherQuery->where('id', 0); // nullify for super roles since they already see all in $pengajuan
        }

        if (isset($allPendingPpbjIds) && $allPendingPpbjIds->isNotEmpty()) {
            $otherQuery->whereNotIn('id', $allPendingPpbjIds);
        }

        $applySearch($otherQuery);

        $otherPengajuan = $otherQuery->latest()->paginate(10, ['*'], 'other_page')->withQueryString();

        return view('tracking.index', compact('pengajuan', 'otherPengajuan', 'pendingApprovals', 'user'));
    }

    public function show(Ppbj $ppbj): View
    {
        $user = Auth::user();

        // Pastikan user berhak melihat dokumen ini
        $this->authorizeView($user, $ppbj);

        $ppbj->load([
            'user.department',
            'latestProposalHarga.internalAgreement',
        ]);

        $timeline = $this->buildTimeline($ppbj);

        return view('tracking.show', compact('ppbj', 'user', 'timeline'));
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Otorisasi akses view detail berdasarkan role.
     */
    private function authorizeView($user, Ppbj $ppbj): void
    {
        // User memohon agar "berikan akses dokumen untuk melihat detail ajuan agar QR bisa terpakai"
        // Artinya SEMUA orang yang bisa login, boleh melihat detail (baca saja),
        // jadi kita hapus blokade abort_if ini.
        // abort_if(! $allowed, 403, 'Anda tidak memiliki akses ke dokumen ini.');
    }

    /**
     * Bangun data timeline berdasarkan status dokumen (PPBJ -> PH -> IA).
     */
    private function buildTimeline(Ppbj $ppbj): array
    {
        $timeline = [];
        $ph = $ppbj->latestProposalHarga;
        $ia = $ph?->internalAgreement;

        // --- PPBJ STAGE ---
        $ppbjStep = (int) $ppbj->approval_step;
        $ppbjStatus = $ppbj->status;

        $timeline[] = $this->makeNode('Pengajuan PPBJ', 'Dibuat oleh ' . $ppbj->user->name, $ppbj->created_at, 'done');
        $timeline[] = $this->makeNode('Review Ka. Dept (PPBJ)', 'Menunggu persetujuan', $ppbjStep > 1 ? $ppbj->updated_at : null, $this->nodeStatus($ppbjStatus, $ppbjStep, 1));
        $timeline[] = $this->makeNode('Review Ka. Divisi (PPBJ)', 'Menunggu persetujuan', $ppbjStep > 2 ? $ppbj->updated_at : null, $this->nodeStatus($ppbjStatus, $ppbjStep, 2));
        $timeline[] = $this->makeNode('Review Accounting (PPBJ)', 'Menunggu persetujuan', $ppbjStep > 3 ? $ppbj->updated_at : null, $this->nodeStatus($ppbjStatus, $ppbjStep, 3));

        // --- PH STAGE ---
        if ($ph) {
            $phStep = (int) $ph->approval_step;
            $phStatus = $ph->status;

            $timeline[] = $this->makeNode('Pengajuan PH', 'Dibuat oleh ' . $ppbj->user->name, $ph->created_at, 'done');
            $timeline[] = $this->makeNode('Review Ka. Dept (PH)', 'Menunggu persetujuan', $phStep > 1 ? $ph->updated_at : null, $this->nodeStatus($phStatus, $phStep, 1));
            $timeline[] = $this->makeNode('Review Ka. Divisi (PH)', 'Menunggu persetujuan', $phStep > 2 ? $ph->updated_at : null, $this->nodeStatus($phStatus, $phStep, 2));
            $timeline[] = $this->makeNode('Review Accounting (PH)', 'Menunggu persetujuan', $phStep > 3 ? $ph->updated_at : null, $this->nodeStatus($phStatus, $phStep, 3));
        } else {
            $timeline[] = $this->makeNode('Pengajuan PH', 'Belum diajukan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Dept (PH)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Divisi (PH)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Accounting (PH)', 'Menunggu persetujuan', null, 'pending');
        }

        // --- IA STAGE ---
        if ($ia) {
            $iaStep = (int) $ia->approval_step;
            $iaStatus = $ia->status_ia;

            $timeline[] = $this->makeNode('Pengajuan IA', 'Dibuat oleh ' . $ppbj->user->name, $ia->created_at, 'done');
            $timeline[] = $this->makeNode('Review Ka. Dept (IA)', 'Menunggu persetujuan', $iaStep > 1 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 1));
            $timeline[] = $this->makeNode('Review Ka. Divisi (IA)', 'Menunggu persetujuan', $iaStep > 2 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 2));
            $timeline[] = $this->makeNode('Review Ka. Dept Accounting (IA)', 'Menunggu persetujuan', $iaStep > 3 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 3));
            $timeline[] = $this->makeNode('Review Ka. Div Accounting (IA)', 'Menunggu persetujuan', $iaStep > 4 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 4));
            $timeline[] = $this->makeNode('Review Finance Director (IA)', 'Menunggu persetujuan', $iaStep > 5 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 5));
            $timeline[] = $this->makeNode('Review Manufacture Director (IA)', 'Menunggu persetujuan', $iaStep > 6 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 6));
            $timeline[] = $this->makeNode('Review President Director (IA)', 'Menunggu persetujuan', $iaStep > 7 ? $ia->updated_at : null, $this->nodeStatus($iaStatus, $iaStep, 7));
            $timeline[] = $this->makeNode('Finalisasi (Realisasi Anggaran)', 'Anggaran dipotong', $iaStatus === 'Approved' ? $ia->updated_at : null, $iaStatus === 'Approved' ? 'done' : 'pending');
        } else {
            $timeline[] = $this->makeNode('Pengajuan IA', 'Belum diajukan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Dept (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Divisi (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Dept Accounting (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Ka. Div Accounting (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Finance Director (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review Manufacture Director (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Review President Director (IA)', 'Menunggu persetujuan', null, 'pending');
            $timeline[] = $this->makeNode('Finalisasi (Realisasi Anggaran)', 'Anggaran dipotong', null, 'pending');
        }

        return $timeline;
    }

    private function makeNode($label, $desc, $date, $status)
    {
        return [
            'label' => $label,
            'description' => $desc,
            'date' => $date ? $date->format('d M Y, H:i') : null,
            'status' => $status,
        ];
    }

    private function nodeStatus($status, $currentStep, $nodeStep)
    {
        if ($status === 'Rejected' && $currentStep === $nodeStep) return 'rejected';
        if ($status === 'Approved') return 'done';
        if ($currentStep > $nodeStep) return 'done';
        if ($currentStep === $nodeStep) return 'active';
        return 'pending';
    }
}