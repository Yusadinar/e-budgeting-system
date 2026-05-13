<?php
// app/Http/Controllers/SuperAdmin/AuditLogController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BudgetLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BudgetLog::with('department')
            ->orderByDesc('created_at');

        // Filter by department
        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }

        // Filter by log type
        if ($request->filled('log_type')) {
            $query->where('log_type', $request->log_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs        = $query->paginate(20)->withQueryString();
        $departments = Department::orderBy('dept_name')->get();
        $logTypes    = ['reserve', 'actual_deduction', 'increase', 'reclass'];

        return view('superadmin.audit.index', compact('logs', 'departments', 'logTypes'));
    }
}
