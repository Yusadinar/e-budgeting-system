<?php

namespace App\Http\Controllers;

use App\Models\BudgetLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetLogController extends Controller
{
    /**
     * Menampilkan daftar semua riwayat pemakaian anggaran untuk departemen user.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Superadmin and Director can see all logs, otherwise filter by department
        $query = BudgetLog::with('department')->orderBy('created_at', 'desc');

        if (!$user->isSuperAdmin() && !$user->isDirector()) {
            $query->where('dept_id', $user->department?->id);
        }

        $logs = $query->paginate(20);

        return view('budget-logs.index', compact('logs'));
    }
}
