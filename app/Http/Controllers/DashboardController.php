<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\CostCenter;
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
        $userCostCenter = $user->costCenter;

        // ── Ambil budget aktif tahun ini ──────────────────────────
        // Prioritas: cost_center_id > dept_id
        $budget = null;
        if ($user->cost_center_id) {
            $budget = AnnualBudget::where('cost_center_id', $user->cost_center_id)
                ->where('fiscal_year', now()->year)
                ->first();
            $sisaPagu          = $budget ? $budget->remaining         : 0;
            $totalReserved     = $budget ? $budget->total_reserved     : 0;
            $totalUsed         = $budget ? $budget->total_used         : 0;
            $totalPlan         = $budget ? $budget->total_plan         : 0;
        } else {
            // Jika tidak ada cost center, gunakan aggregated budget dari departemen
            $sisaPagu          = $department ? $department->remaining_budget : 0;
            $totalReserved     = $department ? $department->total_reserved : 0;
            $totalUsed         = $department ? $department->total_used : 0;
            $totalPlan         = $department ? $department->total_plan : 0;
        }

        // ── Summary Cards ─────────────────────────────────────────
        // 1. PH yang belum Approved
        $phBelumApproved = ProposalHarga::whereHas('ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department?->id);
            })
            ->whereIn('status', ['Draft', 'In_Review'])
            ->sum('nominal_request');

        // 2. IA yang belum Approved
        $iaBelumApproved = InternalAgreement::whereHas('proposalHarga.ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department?->id);
            })
            ->whereIn('status_ia', ['Draft', 'In_Review'])
            ->sum('final_nominal');

        // 3. PH yang sudah Approved TAPI belum punya IA sama sekali
        $phTanpaIa = ProposalHarga::whereHas('ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department?->id);
            })
            ->where('status', 'Approved')
            ->whereDoesntHave('internalAgreement')
            ->sum('nominal_request');

        // Total pengajuan berjalan adalah gabungan dari ketiga kondisi di atas
        $totalPengajuanBerjalan = $phBelumApproved + $iaBelumApproved + $phTanpaIa;

        // Total pengajuan selesai (IA yang sudah Approved)
        $totalPengajuanSelesai = InternalAgreement::whereHas('proposalHarga.ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department?->id);
            })
            ->where('status_ia', 'Approved')
            ->sum('final_nominal');

        // Susun array 12 bulan (1-12)
        $labels        = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        
        // Rencana: tampilkan nilai pagu awal secara utuh sebagai garis target
        $dataRencana   = array_fill(0, 12, $totalPlan);
        
        // Realisasi: ambil dari IA Approved per bulan di tahun ini
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

        // Realisasi: dibuat kumulatif (YTD) untuk melihat progres penyerapan anggaran terhadap pagu
        $dataRealisasi = [];
        $cumulative    = 0;
        $currentMonth  = now()->month;
        for ($m = 1; $m <= 12; $m++) {
            if ($m <= $currentMonth) {
                $cumulative += (float) ($realisasiRaw[$m] ?? 0);
                $dataRealisasi[] = $cumulative;
            } else {
                $dataRealisasi[] = null;
            }
        }

        // ── Pending Reviews Notification (Khusus Approver & Pembuat Dokumen) ────────
        $pendingReviews = $user->getPendingActionPpbjIds()->count();

        // ── Riwayat Transaksi Anggaran (Budget Logs) ───────────────
        $recentBudgetLogsQuery = \App\Models\BudgetLog::query();
        if ($user->cost_center_id) {
            $recentBudgetLogsQuery->where('cost_center_id', $user->cost_center_id);
        } else if ($department) {
            $costCenterIds = $department->costCenters()->pluck('id')->toArray();
            $recentBudgetLogsQuery->where(function($q) use ($department, $costCenterIds) {
                $q->where('dept_id', $department->id)
                  ->orWhereIn('cost_center_id', $costCenterIds);
            });
        }
        
        $recentBudgetLogs = $recentBudgetLogsQuery->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'department',
            'userCostCenter',
            'sisaPagu',
            'totalReserved',
            'totalUsed',
            'totalPlan',
            'totalPengajuanBerjalan',
            'totalPengajuanSelesai',
            'labels',
            'dataRencana',
            'dataRealisasi',
            'pendingReviews',
            'recentBudgetLogs'
        ));
    }
}