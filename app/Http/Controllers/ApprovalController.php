<?php
// app/Http/Controllers/ApprovalController.php

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

use App\Models\PpbjApproval;

class ApprovalController extends Controller
{
    // =========================================================
    // APPROVAL PPBJ (4 Steps)
    // =========================================================
    public function approvePpbj(Request $request, Ppbj $ppbj): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate([
            'signature_data' => ['required', 'string'],
        ], [
            'signature_data.required' => 'Tanda tangan digital wajib diisi sebelum menyetujui.',
        ]);

        $currentStep = (int) $ppbj->approval_step;
        $isFinAcc = str_contains(strtolower($user->department?->dept_name ?? ''), 'finance accounting');
        $isKaSiePurchasing = $user->isKaSie() && $user->section === 'Purchasing & Import';

        $isValid = match($currentStep) {
            1 => $user->isKaDept() && $ppbj->user->dept_id === $user->dept_id,
            2 => $isKaSiePurchasing,
            3 => $user->isKaDiv() && $isFinAcc,
            4 => $user->isFinDir(),
            5 => $user->isPresDir(),
            default => false,
        };

        // Tester all-access: bisa approve semua step PPBJ
        if ($user->isTester()) {
            $isValid = true;
        }

        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui PPBJ ini.');

        // Tentukan batas akhir (max step) berdasarkan nominal
        $range = $ppbj->budget_amount_range;
        $maxStep = 3; // Default sampai Ka. Div Finance (<= 50jt)
        if ($range === '50m_to_100m') {
            $maxStep = 4; // Sampai Finance Director
        } elseif (in_array($range, ['100m_to_500m', '500m_to_1b', 'over_1b'])) {
            $maxStep = 5; // Sampai President Director
        }

        $nextStep = $currentStep + 1;
        $isFinalStep = $currentStep >= $maxStep;

        DB::transaction(function () use ($ppbj, $user, $currentStep, $nextStep, $isFinalStep, $request) {
            // Simpan record approval dengan tanda tangan digital
            PpbjApproval::updateOrCreate(
                ['ppbj_id' => $ppbj->id, 'step' => $currentStep],
                [
                    'user_id'        => $user->id,
                    'action'         => 'approved',
                    'signature_data' => $request->input('signature_data'),
                ]
            );

            $ppbj->update([
                'status'        => $isFinalStep ? 'Approved' : 'In_Review',
                'approval_step' => $nextStep,
            ]);
        });

        return back()->with('success', $isFinalStep ? 'PPBJ berhasil disetujui sepenuhnya.' : 'PPBJ diteruskan ke tahap persetujuan berikutnya.');
    }

    public function rejectPpbj(Request $request, Ppbj $ppbj): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate(['reject_reason' => ['required', 'string', 'max:500']]);

        $currentStep = (int) $ppbj->approval_step;

        DB::transaction(function () use ($ppbj, $user, $currentStep, $request) {
            PpbjApproval::updateOrCreate(
                ['ppbj_id' => $ppbj->id, 'step' => $currentStep],
                [
                    'user_id'       => $user->id,
                    'action'        => 'rejected',
                    'reject_reason' => $request->input('reject_reason'),
                ]
            );

            $ppbj->update(['status' => 'Rejected']);
        });

        return back()->with('warning', 'PPBJ telah ditolak.');
    }

    // =========================================================
    // APPROVAL PROPOSAL HARGA (4 Steps)
    // =========================================================
    public function approvePh(Request $request, ProposalHarga $ph): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate([
            'signature_data' => ['required', 'string'],
        ], [
            'signature_data.required' => 'Tanda tangan digital wajib diisi sebelum menyetujui.',
        ]);

        $currentStep = (int) $ph->approval_step;
        $isFinAcc = str_contains(strtolower($user->department?->dept_name ?? ''), 'finance accounting');

        // Matrix Pembelian:
        // 1. PREPARED: Ka. Sie Purc. -> bramansyah.badar
        // 2. CHECKED: Ka. Sie Proc. -> fauzan.nurdinsyah
        // 3. APPROVED: Ka. Dept Proc & Import -> fadillah.ahmad
        // 4. APPROVED: Ka. Div FA & Proc -> budiwijayanti.riana (max step for <= 100jt)
        // 5. APPROVED: Direktur FA & HCGS -> riana.budiwijayanti (max step for > 100jt - 600jt)
        // 6. APPROVED: Presiden Direktur -> yoga.dina (max step for > 600jt)

        $isValid = match($currentStep) {
            1 => $user->username === 'bramansyah.badar',
            2 => $user->username === 'fauzan.nurdinsyah',
            3 => $user->username === 'fadillah.ahmad',
            4 => $user->username === 'budiwijayanti.riana',
            5 => $user->username === 'riana.budiwijayanti',
            6 => $user->username === 'yoga.dina',
            default => false,
        };

        // Tester all-access: bisa approve semua step PH
        if ($user->isTester()) {
            $isValid = true;
        }

        // For simplicity, we just check the level since we might not have all exact departments seeded correctly.
        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui PH ini atau role tidak sesuai.');

        // Tentukan batas akhir (max step) berdasarkan nominal
        $nominal = (float) $ph->nominal_request;
        $maxStep = 4; // Default sampai Ka Div (<= 100.000.000)
        
        if ($nominal > 100000000 && $nominal <= 600000000) {
            $maxStep = 5; // Sampai Direktur FA & HCGS
        } elseif ($nominal > 600000000) {
            $maxStep = 6; // Sampai Presdir
        }

        $nextStep = $currentStep + 1;
        $isFinalStep = $currentStep >= $maxStep;

        DB::transaction(function () use ($ph, $user, $currentStep, $nextStep, $isFinalStep, $request) {
            \App\Models\PhApproval::updateOrCreate(
                ['ph_id' => $ph->id, 'step' => $currentStep],
                [
                    'user_id'        => $user->id,
                    'action'         => 'approved',
                    'signature_data' => $request->input('signature_data'),
                ]
            );

            $ph->update([
                'status'        => $isFinalStep ? 'Approved' : 'In_Review',
                'approval_step' => $nextStep,
            ]);
        });

        return back()->with('success', $isFinalStep ? 'Proposal Harga berhasil disetujui sepenuhnya.' : 'PH diteruskan ke tahap persetujuan berikutnya.');
    }

    public function rejectPh(Request $request, ProposalHarga $ph): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate(['reject_reason' => ['required', 'string', 'max:500']]);
        
        $currentStep = (int) $ph->approval_step;

        DB::transaction(function () use ($ph, $user, $currentStep, $request) {
            \App\Models\PhApproval::updateOrCreate(
                ['ph_id' => $ph->id, 'step' => $currentStep],
                [
                    'user_id'       => $user->id,
                    'action'        => 'rejected',
                    'reject_reason' => $request->input('reject_reason'),
                ]
            );

            $ph->update(['status' => 'Rejected']);

            // Resolve budget via cost_center_id atau fallback ke dept
            $costCenterId = $ph->cost_center_id;
            $dept   = $ph->ppbj->user->department;
            $budget = null;

            if ($costCenterId) {
                $budget = AnnualBudget::where('cost_center_id', $costCenterId)
                    ->where('fiscal_year', now()->year)
                    ->first();
            }

            if (!$budget) {
                $budget = AnnualBudget::where('dept_id', $dept->id)
                    ->where('fiscal_year', now()->year)
                    ->first();
            }

            if ($budget) {
                $budget->decrement('total_reserved', $ph->nominal_request);

                BudgetLog::record(
                    deptId:      $dept->id,
                    referenceNo: $ph->ph_number,
                    amount:      $ph->nominal_request,
                    logType:     'increase',
                    description: "Reserve dikembalikan — PH ditolak: {$request->reject_reason}",
                    costCenterId: $costCenterId,
                );
            }
        });

        return back()->with('warning', 'Proposal Harga telah ditolak dan reserve anggaran dikembalikan.');
    }

    // =========================================================
    // APPROVAL INTERNAL AGREEMENT (7 Steps)
    // =========================================================
    public function approveIa(Request $request, InternalAgreement $ia): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate([
            'signature_data' => ['required', 'string'],
        ], [
            'signature_data.required' => 'Tanda tangan digital wajib diisi sebelum menyetujui.',
        ]);

        $currentStep = (int) $ia->approval_step;
        $submitter = $ia->proposalHarga->ppbj->user;
        $isFinAcc = str_contains(strtolower($user->department?->dept_name ?? ''), 'finance accounting');

        // Matrix IA (6 Steps) sesuai form:
        // 1. Pemohon: Ka. Sie terkait (dari departemen & seksi si pembuat pengajuan)
        // 2. Disetujui: Ka. Dept terkait
        // 3. Disetujui: Ka. Div terkait
        // 4. Disetujui: Ka. Dept FA
        // 5. Disetujui: Production Director (Man Dir / Pres Dir)
        // 6. Disetujui: Finance Director
        $isValid = match($currentStep) {
            1 => $user->isKaSie() && $user->dept_id === $submitter->dept_id && $user->section === $submitter->section,
            2 => $user->isKaDept() && $user->dept_id === $submitter->dept_id,
            3 => $user->isKaDiv() && $user->dept_id === $submitter->dept_id,
            4 => $user->isKaDept() && $isFinAcc,
            5 => $user->isProdDir() || $user->isManDir() || $user->isPresDir(), // Mengakomodasi label "Production Director"
            6 => $user->isFinDir(),
            default => false,
        };

        // Fallback jika user membuat IA dan dia sendiri adalah Ka. Sie (Self-approve step 1)
        if ($currentStep === 1 && $submitter->id === $user->id && $user->isKaSie()) {
            $isValid = true;
        }

        // Tester all-access: bisa approve semua step IA
        if ($user->isTester()) {
            $isValid = true;
        }

        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui IA ini atau role tidak sesuai.');

        // Menentukan batas step approval IA berdasarkan nominal (seperti PH)
        $nominal = (float) $ia->final_nominal;
        $maxStep = 4; // Default sampai Ka. Dept FA (<= 100.000.000)
        
        if ($nominal > 100000000 && $nominal <= 600000000) {
            $maxStep = 5; // Sampai Production Director
        } elseif ($nominal > 600000000) {
            $maxStep = 6; // Sampai Finance Director
        }

        $nextStep = $currentStep + 1;
        $isFinalStep = $currentStep >= $maxStep;

        DB::transaction(function () use ($ia, $user, $currentStep, $nextStep, $isFinalStep, $request) {
            // Simpan Tanda Tangan
            \App\Models\IaApproval::updateOrCreate(
                ['ia_id' => $ia->id, 'step' => $currentStep],
                [
                    'user_id'        => $user->id,
                    'action'         => 'approved',
                    'signature_data' => $request->input('signature_data'),
                ]
            );

            $ia->update([
                'status_ia'     => $isFinalStep ? 'Approved' : 'In_Review',
                'approval_step' => $nextStep,
            ]);

            if ($isFinalStep) {
                // Pindahkan dari reserved ke actual used
                $costCenterId = $ia->proposalHarga->cost_center_id;
                $dept   = $ia->proposalHarga->ppbj->user->department;
                $budget = null;

                if ($costCenterId) {
                    $budget = AnnualBudget::where('cost_center_id', $costCenterId)
                        ->where('fiscal_year', now()->year)
                        ->first();
                }

                if (!$budget) {
                    $budget = AnnualBudget::where('dept_id', $dept->id)
                        ->where('fiscal_year', now()->year)
                        ->first();
                }

                if ($budget) {
                    $finalNominal = $ia->final_nominal;
                    $budget->decrement('total_reserved', $finalNominal);
                    $budget->increment('total_used', $finalNominal);

                    BudgetLog::record(
                        deptId:      $dept->id,
                        referenceNo: $ia->ia_number,
                        amount:      $finalNominal,
                        logType:     'actual_deduction',
                        description: "Realisasi anggaran — IA disetujui final: {$ia->proposalHarga->subject}",
                        costCenterId: $costCenterId,
                    );
                }
            }
        });

        return back()->with('success', $isFinalStep ? 'Internal Agreement disetujui final dan anggaran telah direalisasikan.' : 'IA diteruskan ke tahap persetujuan berikutnya.');
    }

    public function rejectIa(Request $request, InternalAgreement $ia): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate(['reject_reason' => ['required', 'string', 'max:500']]);

        DB::transaction(function () use ($ia, $request) {
            $ia->update([
                'status_ia' => 'Rejected',
            ]);

            // Resolve budget via cost_center_id atau fallback ke dept
            $costCenterId = $ia->proposalHarga->cost_center_id;
            $dept   = $ia->proposalHarga->ppbj->user->department;
            $budget = null;

            if ($costCenterId) {
                $budget = AnnualBudget::where('cost_center_id', $costCenterId)
                    ->where('fiscal_year', now()->year)
                    ->first();
            }

            if (!$budget) {
                $budget = AnnualBudget::where('dept_id', $dept->id)
                    ->where('fiscal_year', now()->year)
                    ->first();
            }

            if ($budget) {
                $budget->decrement('total_reserved', $ia->final_nominal);

                BudgetLog::record(
                    deptId:      $dept->id,
                    referenceNo: $ia->ia_number,
                    amount:      $ia->final_nominal,
                    logType:     'increase',
                    description: "Reserve dikembalikan — IA ditolak: {$request->reject_reason}",
                    costCenterId: $costCenterId,
                );
            }
        });

        return back()->with('warning', 'Internal Agreement telah ditolak dan reserve anggaran dikembalikan ke sisa pagu.');
    }
}