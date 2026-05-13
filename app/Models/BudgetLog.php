<?php
// app/Models/BudgetLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetLog extends Model
{
    protected $fillable = [
        'dept_id',
        'reference_no',
        'amount',
        'log_type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Log terkait dengan satu department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    // =========================================================
    // SCOPE
    // =========================================================

    public function scopeReserve($query)
    {
        return $query->where('log_type', 'reserve');
    }

    public function scopeActualDeduction($query)
    {
        return $query->where('log_type', 'actual_deduction');
    }

    public function scopeIncrease($query)
    {
        return $query->where('log_type', 'increase');
    }

    public function scopeByDept($query, int $deptId)
    {
        return $query->where('dept_id', $deptId);
    }

    // =========================================================
    // ACCESSOR
    // =========================================================

    public function getLogTypeLabelAttribute(): string
    {
        return match($this->log_type) {
            'reserve'          => 'Hold / Reserve',
            'actual_deduction' => 'Realisasi Terpakai',
            'increase'         => 'Penambahan Pagu',
            'reclass'          => 'Reklasifikasi',
            default            => $this->log_type,
        };
    }

    // =========================================================
    // STATIC: Catat log anggaran (dipanggil dari Observer/Service)
    // =========================================================

    /**
     * Helper statik untuk mencatat perubahan anggaran dengan satu baris.
     *
     * Contoh pemakaian dari Controller/Observer:
     * BudgetLog::record($deptId, $ph->ph_number, $ph->nominal_request, 'reserve', 'Hold PH disetujui Ka.Dept');
     */
    public static function record(
        int    $deptId,
        string $referenceNo,
        float  $amount,
        string $logType,
        string $description = ''
    ): static {
        return static::create([
            'dept_id'      => $deptId,
            'reference_no' => $referenceNo,
            'amount'       => $amount,
            'log_type'     => $logType,
            'description'  => $description,
        ]);
    }
}