<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect superadmin ke panel khusus
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Redirect direktur ke panel monitoring khusus
        if ($user->isDirector()) {
            return redirect()->route('director.dashboard');
        }

        $department = $user->department;

        // ── Ambil budget aktif tahun ini ──────────────────────────
        $budget = AnnualBudget::where('dept_id', $department?->id)
            ->where('fiscal_year', now()->year)
            ->first();

        $sisaPagu          = $budget ? $budget->remaining         : 0;
        $totalReserved     = $budget ? $budget->total_reserved     : 0;
        $totalUsed         = $budget ? $budget->total_used         : 0;
        $totalPlan         = $budget ? $budget->total_plan         : 0;

        // ── Summary Cards ─────────────────────────────────────────
        // 1. PH yang belum Approved
        $phBelumApproved = ProposalHarga::whereHas('ppbj', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereIn('status', ['Draft', 'In_Review'])
            ->sum('nominal_request');

        // 2. IA yang belum Approved
        $iaBelumApproved = InternalAgreement::whereHas('proposalHarga.ppbj', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereIn('status_ia', ['Draft', 'In_Review'])
            ->sum('final_nominal');

        // 3. PH yang sudah Approved TAPI belum punya IA sama sekali
        $phTanpaIa = ProposalHarga::whereHas('ppbj', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', 'Approved')
            ->whereDoesntHave('internalAgreement')
            ->sum('nominal_request');

        // Total pengajuan berjalan adalah gabungan dari ketiga kondisi di atas
        $totalPengajuanBerjalan = $phBelumApproved + $iaBelumApproved + $phTanpaIa;

        // Total pengajuan selesai (IA yang sudah Approved)
        $totalPengajuanSelesai = InternalAgreement::whereHas('proposalHarga.ppbj', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status_ia', 'Approved')
            ->sum('final_nominal');

        // ── Data Grafik per Bulan (Rencana vs Realisasi) ─────────
        // Rencana: distribusi rata pagu per bulan
        $rencanaPerBulan = $totalPlan > 0
            ? round($totalPlan / 12, 2)
            : 0;

        // Realisasi: IA Approved per bulan di tahun ini
        $realisasiQuery = InternalAgreement::where('status_ia', 'Approved')
            ->whereYear('updated_at', now()->year);

        // Jika bukan direktur, filter per departemen
        if (!$user->isDirector()) {
            $realisasiQuery->whereHas('proposalHarga.ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department?->id);
            });
        }

        $realisasiRaw = $realisasiQuery->selectRaw('MONTH(updated_at) as bulan, SUM(final_nominal) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // Susun array 12 bulan (1-12), isi 0 jika tidak ada realisasi
        $labels      = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataRencana = array_fill(0, 12, $rencanaPerBulan);
        $dataRealisasi = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataRealisasi[] = (float) ($realisasiRaw[$m] ?? 0);
        }

        // ── Pending Reviews Notification (Khusus Approver) ────────
        $pendingReviews = 0;
        if ($user->canApprove()) {
            $role = $user->role;
            $deptId = $user->dept_id;

            // PPBJ Pending
            $ppbjQuery = \App\Models\Ppbj::where('status', 'In_Review')
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
                    // Jika direktur atau role lain yang canApprove tapi tidak ada di step PPBJ, biarkan kosong
                    if (!$user->isKaDept() && !$user->isKaDiv() && !$user->isAccounting()) {
                        $q->where('id', 0);
                    }
                });
            $pendingReviews += $ppbjQuery->count();

            // PH Pending
            $phQuery = \App\Models\ProposalHarga::where('status', 'In_Review')
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
                });
            $pendingReviews += $phQuery->count();

            // IA Pending
            $iaQuery = \App\Models\InternalAgreement::where('status_ia', 'In_Review')
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
                });
            $pendingReviews += $iaQuery->count();
        }

        return view('dashboard', compact(
            'user',
            'department',
            'sisaPagu',
            'totalReserved',
            'totalPlan',
            'totalPengajuanBerjalan',
            'totalPengajuanSelesai',
            'labels',
            'dataRencana',
            'dataRealisasi',
            'pendingReviews'
        ));
    }
}