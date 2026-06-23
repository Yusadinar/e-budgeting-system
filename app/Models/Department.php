<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Department extends Model
{
    protected $fillable = [
        'dept_name',
        'budget_code',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Satu department memiliki banyak users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'dept_id');
    }

    /**
     * Satu department memiliki banyak cost centers.
     */
    public function costCenters(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'dept_id');
    }

    /**
     * Satu department memiliki satu annual budget per tahun.
     * Gunakan ->ofYear($year) untuk filter tahun tertentu.
     */
    public function annualBudgets(): HasMany
    {
        return $this->hasMany(AnnualBudget::class, 'dept_id');
    }

    /**
     * Shortcut: Budget aktif untuk tahun fiskal saat ini.
     */
    public function currentBudget(): HasOne
    {
        return $this->hasOne(AnnualBudget::class, 'dept_id')
                    ->where('fiscal_year', now()->year)
                    ->whereNull('cost_center_id');
    }

    /**
     * Satu department memiliki banyak budget logs (audit trail).
     */
    public function budgetLogs(): HasMany
    {
        return $this->hasMany(BudgetLog::class, 'dept_id');
    }

    /**
     * Aggregated Budget (Department Global + all Cost Centers under this department)
     */
    public function getTotalPlanAttribute(): float
    {
        $deptPlan = $this->currentBudget ? $this->currentBudget->total_plan : 0;
        $ccPlan = $this->costCenters->reduce(function ($carry, $cc) {
            return $carry + ($cc->currentBudget ? $cc->currentBudget->total_plan : 0);
        }, 0);
        return $deptPlan + $ccPlan;
    }

    public function getTotalUsedAttribute(): float
    {
        $deptUsed = $this->currentBudget ? $this->currentBudget->total_used : 0;
        $ccUsed = $this->costCenters->reduce(function ($carry, $cc) {
            return $carry + ($cc->currentBudget ? $cc->currentBudget->total_used : 0);
        }, 0);
        return $deptUsed + $ccUsed;
    }

    public function getTotalReservedAttribute(): float
    {
        $deptReserved = $this->currentBudget ? $this->currentBudget->total_reserved : 0;
        $ccReserved = $this->costCenters->reduce(function ($carry, $cc) {
            return $carry + ($cc->currentBudget ? $cc->currentBudget->total_reserved : 0);
        }, 0);
        return $deptReserved + $ccReserved;
    }

    /**
     * Hitung sisa pagu anggaran aktif (total_plan - total_used - total_reserved).
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->total_plan - $this->total_used - $this->total_reserved;
    }
}