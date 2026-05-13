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
                    ->where('fiscal_year', now()->year);
    }

    /**
     * Satu department memiliki banyak budget logs (audit trail).
     */
    public function budgetLogs(): HasMany
    {
        return $this->hasMany(BudgetLog::class, 'dept_id');
    }

    // =========================================================
    // ACCESSOR / HELPER
    // =========================================================

    /**
     * Hitung sisa pagu anggaran aktif (total_plan - total_used - total_reserved).
     */
    public function getRemainingBudgetAttribute(): float
    {
        $budget = $this->currentBudget;
        if (! $budget) return 0;

        return $budget->total_plan - $budget->total_used - $budget->total_reserved;
    }
}