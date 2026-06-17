<?php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\CostCenter;
use App\Models\Ppbj;
use App\Models\ProposalHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalHargaController extends Controller
{
    public function create(Ppbj $ppbj)
    {
        $user = Auth::user();

        $isKaSiePurchasing = $user->username === 'bramansyah.badar' || ($user->isKaSie() && $user->section === 'Purchasing & Import');
        abort_if(!$isKaSiePurchasing, 403, 'Hanya Ka. Sie Purchasing yang dapat membuat Proposal Harga.');
        abort_if(!$ppbj->isApproved(), 403, 'PPBJ belum sepenuhnya disetujui.');

        // Ambil budget berdasarkan cost center user (jika ada) atau dept_id
        $budget = $this->getUserBudget($user);

        // Ambil cost centers berdasarkan departemen dari pembuat PPBJ
        $costCenters = CostCenter::where('dept_id', $ppbj->user->department?->id)
            ->orderBy('cost_center_name')
            ->get();

        // Jika user sudah di-assign ke cost center tertentu, readonly
        $userCostCenter = $user->costCenter;

        return view('proposal-harga.create', compact('ppbj', 'budget', 'costCenters', 'userCostCenter'));
    }

    public function store(Request $request, Ppbj $ppbj)
    {
        $user = Auth::user();
        $isKaSiePurchasing = $user->username === 'bramansyah.badar' || ($user->isKaSie() && $user->section === 'Purchasing & Import');
        abort_if(!$isKaSiePurchasing, 403, 'Akses ditolak. Hanya Ka. Sie Purchasing yang dapat membuat PH.');

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'section_name' => ['nullable', 'string', 'max:255'],
            'cost_center' => ['nullable', 'string', 'max:255'],
            'cost_center_id' => ['required', 'exists:cost_centers,id'],
            'no_io_asset' => ['nullable', 'string', 'max:255'],
            
            'items_data' => ['required', 'string'],
            
            'delivery_time' => ['nullable', 'string', 'max:255'],
            'quality' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'experience_non_ippi' => ['nullable', 'string', 'max:255'],
            'selected_vendor_name' => ['nullable', 'string', 'max:255'],
            'nominal_request' => ['required', 'numeric', 'min:1'],
        ]);

        $user = Auth::user();
        $costCenterId = (int) $validated['cost_center_id'];

        // Cari budget berdasarkan cost_center_id
        $budget = AnnualBudget::where('cost_center_id', $costCenterId)
            ->where('fiscal_year', now()->year)
            ->first();

        // Fallback ke dept_id jika cost center belum punya budget
        if (!$budget) {
            $budget = AnnualBudget::where('dept_id', $user->department?->id)
                ->where('fiscal_year', now()->year)
                ->first();
        }

        if (!$budget) {
            return back()->withErrors([
                'cost_center_id' => 'Tidak ditemukan pagu anggaran untuk Cost Center yang dipilih di tahun ini.',
            ])->withInput();
        }

        if (! $budget->isWithinBudget((float) $validated['nominal_request'])) {
            return back()->withErrors([
                'nominal_request' => 'Nominal melebihi sisa pagu anggaran (' . number_format($budget->remaining, 2, ',', '.') . ').',
            ])->withInput();
        }

        $itemsDataArray = json_decode($validated['items_data'], true);

        // Ambil cost center info untuk label
        $cc = CostCenter::find($costCenterId);

        $ph = \Illuminate\Support\Facades\DB::transaction(function() use ($ppbj, $costCenterId, $validated, $cc, $itemsDataArray, $request, $budget) {
            $ph = ProposalHarga::create([
                'ppbj_id' => $ppbj->id,
                'cost_center_id' => $costCenterId,
                'ph_number' => ProposalHarga::generateNumber(),
                'subject' => $validated['subject'],
                'nominal_request' => $validated['nominal_request'],
                'approval_step' => 1,
                'status' => 'In_Review',
                
                'type' => $validated['type'] ?? null,
                'department_name' => $validated['department_name'] ?? null,
                'section_name' => $validated['section_name'] ?? null,
                'cost_center' => $cc ? $cc->dropdown_label : ($validated['cost_center'] ?? null),
                'no_io_asset' => $validated['no_io_asset'] ?? null,
                'items_data' => $itemsDataArray,
                
                'delivery_time' => $validated['delivery_time'] ?? null,
                'quality' => $validated['quality'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'experience_non_ippi' => $validated['experience_non_ippi'] ?? null,
                
                'selected_vendor_name' => $validated['selected_vendor_name'] ?? null,
                'signature_data' => $request->input('signature_data'),
            ]);

            $budget->increment('total_reserved', $ph->nominal_request);
            BudgetLog::record(
                deptId: $budget->dept_id ?? $cc?->dept_id ?? $user->dept_id ?? 0,
                referenceNo: $ph->ph_number,
                amount: $ph->nominal_request,
                logType: 'reserve',
                description: "Hold anggaran dari pengajuan Proposal Harga: {$ph->subject} (CC: {$cc->cost_center_name})",
                costCenterId: $costCenterId
            );

            return $ph;
        });

        return redirect()->route('pengajuan.index')
            ->with('success', "Proposal Harga {$ph->ph_number} berhasil diajukan dan menunggu persetujuan.");
    }
    
    public function print(ProposalHarga $proposalHarga)
    {
        $proposalHarga->load('costCenterRelation');
        return view('proposal-harga.print', compact('proposalHarga'));
    }

    /**
     * Helper: Ambil budget aktif user (prioritas cost center > dept).
     */
    private function getUserBudget($user): ?AnnualBudget
    {
        // Prioritas 1: Budget dari cost center yang di-assign
        if ($user->cost_center_id) {
            $budget = AnnualBudget::where('cost_center_id', $user->cost_center_id)
                ->where('fiscal_year', now()->year)
                ->first();
            if ($budget) return $budget;
        }

        // Prioritas 2: Budget dari dept_id (backward compat)
        return AnnualBudget::where('dept_id', $user->department?->id)
            ->where('fiscal_year', now()->year)
            ->first();
    }
}
