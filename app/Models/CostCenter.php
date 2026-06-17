<?php
// app/Models/CostCenter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CostCenter extends Model
{
    protected $fillable = [
        'department_group',
        'plant',
        'expense_type',
        'cost_center_code',
        'cost_center_name',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Satu Cost Center memiliki banyak annual budget (per tahun fiskal).
     */
    public function annualBudgets(): HasMany
    {
        return $this->hasMany(AnnualBudget::class, 'cost_center_id');
    }

    /**
     * Departemen parent (jika ada).
     */
    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    /**
     * Shortcut: Budget aktif untuk tahun fiskal saat ini.
     */
    public function currentBudget(): HasOne
    {
        return $this->hasOne(AnnualBudget::class, 'cost_center_id')
                    ->where('fiscal_year', now()->year);
    }

    /**
     * User yang di-assign ke cost center ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cost_center_id');
    }

    /**
     * Budget logs terkait cost center ini.
     */
    public function budgetLogs(): HasMany
    {
        return $this->hasMany(BudgetLog::class, 'cost_center_id');
    }

    // =========================================================
    // ACCESSOR / HELPER
    // =========================================================

    /**
     * Label lengkap: "IBEK Line A (P-902-3711)"
     */
    public function getFullLabelAttribute(): string
    {
        $code = $this->cost_center_code ? " ({$this->cost_center_code})" : '';
        return "{$this->cost_center_name}{$code}";
    }

    /**
     * Label untuk dropdown: "P-902-3711 — IBEK Line A"
     */
    public function getDropdownLabelAttribute(): string
    {
        $code = $this->cost_center_code ?? 'N/A';
        return "{$code} — {$this->cost_center_name}";
    }

    /**
     * Plant label yang mudah dibaca.
     */
    public function getPlantLabelAttribute(): string
    {
        return match($this->plant) {
            'IBEK' => 'Bekasi (IBEK)',
            'IKAR' => 'Karawang (IKAR)',
            'HO'   => 'Head Office (HO)',
            default => $this->plant,
        };
    }

    /**
     * Hitung sisa pagu anggaran aktif.
     */
    public function getRemainingBudgetAttribute(): float
    {
        $budget = $this->currentBudget;
        if (! $budget) return 0;

        return $budget->total_plan - $budget->total_used - $budget->total_reserved;
    }
}
