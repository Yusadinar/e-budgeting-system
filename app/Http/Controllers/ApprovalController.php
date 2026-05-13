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

class ApprovalController extends Controller
{
    // =========================================================
    // APPROVAL PPBJ (3 Steps)
    // =========================================================
    public function approvePpbj(Request $request, Ppbj $ppbj): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $currentStep = (int) $ppbj->approval_step;

        $isValid = match($currentStep) {
            1 => $user->isKaDept(),
            2 => $user->isKaDiv(),
            3 => $user->isAccounting(),
            default => false,
        };

        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui PPBJ ini.');

        $nextStep = $currentStep + 1;
        $isFinalStep = $nextStep > 3;

        $ppbj->update([
            'status'        => $isFinalStep ? 'Approved' : 'In_Review',
            'approval_step' => $nextStep,
        ]);

        return back()->with('success', $isFinalStep ? 'PPBJ berhasil disetujui sepenuhnya.' : 'PPBJ diteruskan ke tahap persetujuan berikutnya.');
    }

    public function rejectPpbj(Request $request, Ppbj $ppbj): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate(['reject_reason' => ['required', 'string', 'max:500']]);

        $ppbj->update(['status' => 'Rejected']);

        return back()->with('warning', 'PPBJ telah ditolak.');
    }

    // =========================================================
    // APPROVAL PROPOSAL HARGA (3 Steps)
    // =========================================================
    public function approvePh(Request $request, ProposalHarga $ph): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $currentStep = (int) $ph->approval_step;

        $isValid = match($currentStep) {
            1 => $user->isKaDept(),
            2 => $user->isKaDiv(),
            3 => $user->isAccounting(),
            default => false,
        };

        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui PH ini.');

        $nextStep = $currentStep + 1;
        $isFinalStep = $nextStep > 3;

        $ph->update([
            'status'        => $isFinalStep ? 'Approved' : 'In_Review',
            'approval_step' => $nextStep,
        ]);

        return back()->with('success', $isFinalStep ? 'Proposal Harga berhasil disetujui sepenuhnya.' : 'PH diteruskan ke tahap persetujuan berikutnya.');
    }

    public function rejectPh(Request $request, ProposalHarga $ph): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $request->validate(['reject_reason' => ['required', 'string', 'max:500']]);

        $ph->update(['status' => 'Rejected']);

        return back()->with('warning', 'Proposal Harga telah ditolak.');
    }

    // =========================================================
    // APPROVAL INTERNAL AGREEMENT (7 Steps)
    // =========================================================
    public function approveIa(Request $request, InternalAgreement $ia): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user->canApprove(), 403, 'Anda tidak memiliki hak approval.');

        $currentStep = (int) $ia->approval_step;

        $isValid = match($currentStep) {
            1 => $user->isKaDept(),
            2 => $user->isKaDiv(),
            3 => $user->isKaDeptAcc(),
            4 => $user->isKaDivAcc(),
            5 => $user->isFinDir(),
            6 => $user->isManDir(),
            7 => $user->isPresDir(),
            default => false,
        };

        abort_if(!$isValid, 422, 'Bukan giliran Anda untuk menyetujui IA ini.');

        DB::transaction(function () use ($ia, $user, $currentStep) {
            $nextStep = $currentStep + 1;
            $isFinalStep = $nextStep > 7;

            $ia->update([
                'status_ia'     => $isFinalStep ? 'Approved' : 'In_Review',
                'approval_step' => $nextStep,
            ]);

            if ($isFinalStep) {
                // Pindahkan dari reserved ke actual used
                $dept   = $ia->proposalHarga->ppbj->user->department;
                $budget = AnnualBudget::where('dept_id', $dept->id)
                    ->where('fiscal_year', now()->year)
                    ->first();

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
                    );
                }
            }
        });

        $nextStep = $currentStep + 1;
        $isFinalStep = $nextStep > 7;

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

            // Lepaskan reserve anggaran karena ditolak
            $dept   = $ia->proposalHarga->ppbj->user->department;
            $budget = AnnualBudget::where('dept_id', $dept->id)
                ->where('fiscal_year', now()->year)
                ->first();

            if ($budget) {
                $budget->decrement('total_reserved', $ia->final_nominal);

                BudgetLog::record(
                    deptId:      $dept->id,
                    referenceNo: $ia->ia_number,
                    amount:      $ia->final_nominal,
                    logType:     'increase',
                    description: "Reserve dikembalikan — IA ditolak: {$request->reject_reason}",
                );
            }
        });

        return back()->with('warning', 'Internal Agreement telah ditolak dan reserve anggaran dikembalikan ke sisa pagu.');
    }
}