<?php
// app/Models/InternalAgreement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalAgreement extends Model
{
    use SoftDeletes;

    protected $table = 'internal_agreements';

    protected $fillable = [
        'ph_id',
        'ia_number',
        'sap_doc_no',
        'final_nominal',
        'approval_step',
        'status_ia',
    ];

    protected function casts(): array
    {
        return [
            'final_nominal' => 'decimal:2',
            'approval_step' => 'integer',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * IA berasal dari satu Proposal Harga.
     */
    public function proposalHarga(): BelongsTo
    {
        return $this->belongsTo(ProposalHarga::class, 'ph_id');
    }

    /**
     * Shortcut: Akses Department melalui chain relasi PH -> PPBJ -> User -> Department.
     */
    public function getDepartmentAttribute(): ?Department
    {
        return $this->proposalHarga?->ppbj?->user?->department;
    }

    // =========================================================
    // SCOPE
    // =========================================================

    public function scopeApproved($query)
    {
        return $query->where('status_ia', 'Approved');
    }

    public function scopeInReview($query)
    {
        return $query->where('status_ia', 'In_Review');
    }

    // =========================================================
    // HELPER
    // =========================================================

    public function isClosed(): bool
    {
        return $this->status_ia === 'Approved' && ! is_null($this->sap_doc_no);
    }

    public function getNextApproverLabelAttribute(): string
    {
        return match((int) $this->approval_step) {
            1       => 'Ka. Dept',
            2       => 'Ka. Divisi',
            3       => 'Ka. Dept Accounting',
            4       => 'Ka. Div Accounting',
            5       => 'Finance Director',
            6       => 'Manufacture Director',
            7       => 'President Director',
            8       => 'Final — IA Disetujui',
            default => 'Unknown',
        };
    }

    public function isApproved(): bool
    {
        return $this->status_ia === 'Approved';
    }

    // =========================================================
    // STATIC HELPER: Generate IA Number
    // =========================================================

    /**
     * Generate nomor IA otomatis.
     * Format: 001/IA/IPPI/Jan/2024
     */
    public static function generateNumber(): string
    {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $monthRoman = $romans[now()->month - 1];
        $year  = now()->year;
        
        // Ambil record terakhir secara keseluruhan agar nomor terus berlanjut
        $lastRecord = static::withTrashed()
                            ->orderBy('id', 'desc')
                            ->first();

        $seq = 1;
        if ($lastRecord && preg_match('/^(\d{3})\//', $lastRecord->ia_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        $seqStr = str_pad($seq, 3, '0', STR_PAD_LEFT);
        return "{$seqStr}/IA/IPPI/{$monthRoman}/{$year}";
    }

    public function approvals()
    {
        return $this->hasMany(IaApproval::class, 'ia_id')->orderBy('step', 'asc');
    }
}