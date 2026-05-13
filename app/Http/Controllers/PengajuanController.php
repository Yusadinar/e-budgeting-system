<?php
// app/Http/Controllers/PengajuanController.php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\InternalAgreement;
use App\Models\Ppbj;
use App\Models\ProposalHarga;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    // =========================================================
    // STEP 1 — Form PPBJ (Document style)
    // =========================================================

    /**
     * Tampilkan form dokumen PPBJ bergaya kertas.
     */
    public function createPpbj(): View
    {
        $user        = Auth::user();
        $budget      = AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->first();
        $departments = Department::orderBy('dept_name')->pluck('dept_name', 'id');
        $nextNumber  = Ppbj::generateNumber();

        return view('pengajuan.step1-ppbj', compact('user', 'budget', 'departments', 'nextNumber'));
    }

    /**
     * Simpan PPBJ lengkap dan redirect ke index.
     */
    public function storePpbj(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_section'   => ['required', 'string', 'max:255'],
            'subject'              => ['required', 'string', 'max:255'],
            'nama_barang_jasa'     => ['required', 'string', 'max:255'],
            'spesifikasi'          => ['required', 'string'],
            'qty'                  => ['required', 'integer', 'min:1'],
            'uom'                  => ['required', 'string', 'max:50'],
            'pernah_order'         => ['required', 'in:sudah,belum'],
            'pernah_order_bulan'   => ['nullable', 'string', 'max:50'],
            // 5W+1H
            'bg_what'              => ['required', 'string'],
            'bg_why'               => ['required', 'string'],
            'bg_when'              => ['required', 'string'],
            'bg_where'             => ['required', 'string'],
            'bg_who'               => ['required', 'string'],
            'bg_how'               => ['required', 'string'],
            // Risk Analysis
            'risk_analysis'        => ['required', 'string'],
            // Condition photo
            'condition_photo'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            // Detail Spec
            'spec_brand'           => ['required', 'string', 'max:255'],
            'spec_maker'           => ['required', 'string', 'max:255'],
            'spec_negara_asal'     => ['required', 'string', 'max:255'],
            'spec_lain_lain'       => ['nullable', 'string'],
            // Urgency
            'urgency_level'        => ['required', 'in:low,medium,high'],
            'potensi_line_stop'    => ['nullable', 'string', 'max:100'],
            'urgency_options'      => ['nullable', 'array'],
            'urgency_options.*'    => ['in:tidak_ada_backup,pengadaan_baru,penggantian_rusak,schedule_general_check'],
            'pengadaan_baru_untuk' => ['nullable', 'string', 'max:255'],
            'schedule_general_check' => ['nullable', 'date'],
            // Budget
            'budget_type'          => ['required', 'in:capex,foh,opex,project'],
            'budget_amount_range'  => ['required', 'string'],
            'capex_attachment'     => ['nullable', 'file', 'max:10240'],
            // Layout Area
            'layout_photo'         => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'lokasi_pressline'     => ['nullable', 'string', 'max:255'],
            'lokasi_sub_assy'      => ['nullable', 'string', 'max:255'],
            'lokasi_metal_finish'  => ['nullable', 'string', 'max:255'],
            'lokasi_lain_lain'     => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $budgetExists = AnnualBudget::where('dept_id', $user->dept_id)
            ->where('fiscal_year', now()->year)
            ->exists();

        if (! $budgetExists) {
            return back()->with('warning', 'Departemen Anda belum memiliki pagu anggaran untuk tahun ini. Silakan hubungi Kepala Departemen Anda untuk melakukan input budget terlebih dahulu.')->withInput();
        }

        // Handle file uploads
        $conditionPhotoPath = null;
        if ($request->hasFile('condition_photo')) {
            $conditionPhotoPath = $request->file('condition_photo')
                ->store('ppbj/condition', 'public');
        }

        $layoutPhotoPath = null;
        if ($request->hasFile('layout_photo')) {
            $layoutPhotoPath = $request->file('layout_photo')
                ->store('ppbj/layout', 'public');
        }

        $capexAttachmentPath = null;
        if ($request->hasFile('capex_attachment')) {
            $capexAttachmentPath = $request->file('capex_attachment')
                ->store('ppbj/capex', 'public');
        }

        $ppbj = Ppbj::create([
            'user_id'               => Auth::id(),
            'ppbj_number'           => Ppbj::generateNumber(),
            'jenis_pengeluaran'     => 'FR', // Default, can be changed later
            'approval_step'         => 1,
            'status'                => 'In_Review',
            // Form data
            'department_section'    => $validated['department_section'],
            'subject'               => $validated['subject'],
            'nama_barang_jasa'      => $validated['nama_barang_jasa'],
            'spesifikasi'           => $validated['spesifikasi'],
            'qty'                   => $validated['qty'],
            'uom'                   => $validated['uom'],
            'pernah_order'          => $validated['pernah_order'],
            'pernah_order_bulan'    => $validated['pernah_order_bulan'] ?? null,
            // 5W+1H
            'bg_what'               => $validated['bg_what'],
            'bg_why'                => $validated['bg_why'],
            'bg_when'               => $validated['bg_when'],
            'bg_where'              => $validated['bg_where'],
            'bg_who'                => $validated['bg_who'],
            'bg_how'                => $validated['bg_how'],
            // Risk
            'risk_analysis'         => $validated['risk_analysis'],
            // Photos
            'condition_photo'       => $conditionPhotoPath,
            'layout_photo'          => $layoutPhotoPath,
            // Detail Spec
            'spec_brand'            => $validated['spec_brand'],
            'spec_maker'            => $validated['spec_maker'],
            'spec_negara_asal'      => $validated['spec_negara_asal'],
            'spec_lain_lain'        => $validated['spec_lain_lain'] ?? null,
            // Urgency
            'urgency_level'         => $validated['urgency_level'],
            'potensi_line_stop'     => $validated['potensi_line_stop'] ?? null,
            'urgency_options'       => $validated['urgency_options'] ?? [],
            'pengadaan_baru_untuk'  => $validated['pengadaan_baru_untuk'] ?? null,
            'schedule_general_check'=> $validated['schedule_general_check'] ?? null,
            // Budget
            'budget_type'           => $validated['budget_type'],
            'budget_amount_range'   => $validated['budget_amount_range'],
            'capex_attachment'      => $capexAttachmentPath,
            // Layout
            'lokasi_pressline'      => $validated['lokasi_pressline'] ?? null,
            'lokasi_sub_assy'       => $validated['lokasi_sub_assy'] ?? null,
            'lokasi_metal_finish'   => $validated['lokasi_metal_finish'] ?? null,
            'lokasi_lain_lain'      => $validated['lokasi_lain_lain'] ?? null,
        ]);

        return redirect()->route('pengajuan.index')
            ->with('success', "PPBJ {$ppbj->ppbj_number} berhasil dibuat dan menunggu persetujuan.");
    }

    /**
     * Tampilkan detail PPBJ dalam bentuk dokumen (printable).
     */
    public function showPpbj(Ppbj $ppbj): View
    {
        $ppbj->load('user.department');
        return view('pengajuan.show-ppbj', compact('ppbj'));
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