<?php
// app/Models/AnnualBudget.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualBudget extends Model
{
    protected $fillable = [
        'dept_id',
        'fiscal_year',
        'total_plan',
        'total_used',
        'total_reserved',
    ];

    protected function casts(): array
    {
        return [
            'total_plan'     => 'decimal:2',
            'total_used'     => 'decimal:2',
            'total_reserved' => 'decimal:2',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Annual budget dimiliki oleh satu department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    // =========================================================
    // ACCESSOR / HELPER
    // =========================================================

    /**
     * Sisa pagu yang tersedia (belum dipakai dan belum di-hold).
     */
    public function getRemainingAttribute(): float
    {
        return (float) $this->total_plan
             - (float) $this->total_used
             - (float) $this->total_reserved;
    }

    /**
     * Persentase utilisasi anggaran.
     */
    public function getUtilizationPercentAttribute(): float
    {
        if ((float) $this->total_plan === 0.0) return 0.0;

        return round(
            ((float) $this->total_used / (float) $this->total_plan) * 100,
            2
        );
    }

    /**
     * Cek apakah nominal yang diajukan masih dalam pagu.
     */
    public function isWithinBudget(float $amount): bool
    {
        return $amount <= $this->remaining;
    }
}