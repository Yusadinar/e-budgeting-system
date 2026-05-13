<?php
// app/Http/Controllers/PengajuanController.php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\InternalAgreement;
use App\Models\Ppbj;
use App\Models\ProposalHarga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    // =========================================================
    // STEP 1 — Form PPBJ
    // =========================================================

    /**
     * Tampilkan form wizard Step 1: Input PPBJ.
     */
    public function createPpbj(): View
    {
        $user   = Auth::user();
        $budget = AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->first();

        return view('pengajuan.step1-ppbj', compact('user', 'budget'));
    }

    /**
     * Simpan PPBJ dan redirect ke Step 2.
     */
    public function storePpbj(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_pengeluaran' => ['required', 'in:FR,IR,IO'],
        ]);

        $ppbj = Ppbj::create([
            'user_id'           => Auth::id(),
            'jenis_pengeluaran' => $validated['jenis_pengeluaran'],
            'ppbj_number'       => Ppbj::generateNumber(),
            'approval_step'     => 1,
            'status'            => 'In_Review',
        ]);

        return redirect()->route('pengajuan.index')
            ->with('success', "PPBJ {$ppbj->ppbj_number} berhasil dibuat dan menunggu persetujuan.");
    }

    // =========================================================
    // STEP 2 — Form Proposal Harga (PH)
    // =========================================================

    /**
     * Tampilkan form wizard Step 2: Input Proposal Harga.
     */
    public function createPh(Ppbj $ppbj): View
    {
        $user   = Auth::user();
        $budget = AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->first();

        // Pastikan PPBJ milik user yang login
        abort_if($ppbj->user_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if(!$ppbj->isApproved(), 403, 'PPBJ belum sepenuhnya disetujui.');

        return view('pengajuan.step2-ph', compact('ppbj', 'budget'));
    }

    /**
     * Simpan Proposal Harga dan redirect ke Step 3.
     */
    public function storePh(Request $request, Ppbj $ppbj): RedirectResponse
    {
        abort_if($ppbj->user_id !== Auth::id(), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'subject'         => ['required', 'string', 'max:255'],
            'nominal_request' => ['required', 'numeric', 'min:1'],
        ]);

        // Validasi apakah nominal melebihi sisa budget
        $user   = Auth::user();
        $budget = AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->firstOrFail();

        if (! $budget->isWithinBudget((float) $validated['nominal_request'])) {
            return back()->withErrors([
                'nominal_request' => 'Nominal melebihi sisa pagu anggaran ('
                    . number_format($budget->remaining, 2, ',', '.') . ').',
            ])->withInput();
        }

        $ph = ProposalHarga::create([
            'ppbj_id'         => $ppbj->id,
            'ph_number'       => ProposalHarga::generateNumber(),
            'subject'         => $validated['subject'],
            'nominal_request' => $validated['nominal_request'],
            'approval_step'   => 1,
            'status'          => 'In_Review',
            // qr_token di-generate otomatis di Model::booted()
        ]);

        return redirect()->route('pengajuan.index')
            ->with('success', "Proposal Harga {$ph->ph_number} berhasil diajukan dan menunggu persetujuan.");
    }

    // =========================================================
    // STEP 3 — Form Internal Agreement (IA)
    // =========================================================

    /**
     * Tampilkan form wizard Step 3: Input Internal Agreement.
     */
    public function createIa(ProposalHarga $proposalHarga): View
    {
        abort_if($proposalHarga->ppbj->user_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if(!$proposalHarga->isApproved(), 403, 'Proposal Harga belum sepenuhnya disetujui.');

        return view('pengajuan.step3-ia', compact('proposalHarga'));
    }

    /**
     * Simpan Internal Agreement dan redirect ke halaman tracking.
     */
    public function storeIa(Request $request, ProposalHarga $proposalHarga): RedirectResponse
    {
        abort_if($proposalHarga->ppbj->user_id !== Auth::id(), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'final_nominal' => ['required', 'numeric', 'min:1'],
            'sap_doc_no'    => ['nullable', 'string', 'max:50'],
        ]);

        $user   = Auth::user();
        $budget = AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->firstOrFail();

        if (! $budget->isWithinBudget((float) $validated['final_nominal'])) {
            return back()->withErrors([
                'final_nominal' => 'Nominal melebihi sisa pagu anggaran ('
                    . number_format($budget->remaining, 2, ',', '.') . ').',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $proposalHarga, $budget, $user) {
            $ia = InternalAgreement::create([
                'ph_id'         => $proposalHarga->id,
                'ia_number'     => InternalAgreement::generateNumber(),
                'sap_doc_no'    => $validated['sap_doc_no'] ?? null,
                'final_nominal' => $validated['final_nominal'],
                'approval_step' => 1,
                'status_ia'     => 'In_Review',
            ]);

            // Hold anggaran sementara (reserve) saat IA dibuat
            $budget->increment('total_reserved', $ia->final_nominal);

            BudgetLog::record(
                deptId:      $user->department->id,
                referenceNo: $ia->ia_number,
                amount:      $ia->final_nominal,
                logType:     'reserve',
                description: "Reserve anggaran untuk IA: {$proposalHarga->subject}",
            );
        });

        return redirect()->route('tracking.index')
            ->with('success', 'Internal Agreement berhasil dibuat dan masuk ke proses review.');
    }

    // =========================================================
    // LIST — Semua pengajuan milik user
    // =========================================================

    /**
     * Tampilkan daftar semua PPBJ beserta status PH dan IA-nya.
     */
    public function index(): View
    {
        $pengajuan = Ppbj::where('user_id', Auth::id())
            ->with([
                'latestProposalHarga.internalAgreement',
            ])
            ->latest()
            ->paginate(10);

        return view('pengajuan.index', compact('pengajuan'));
    }
}