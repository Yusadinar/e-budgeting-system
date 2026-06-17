<?php
// app/Models/ProposalHarga.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProposalHarga extends Model
{
    use SoftDeletes;

    protected $table = 'proposal_harga';

    protected $fillable = [
        'ppbj_id',
        'cost_center_id',
        'ph_number',
        'subject',
        'nominal_request',
        'qr_token',
        'approval_step',
        'status',
        'type',
        'department_name',
        'section_name',
        'cost_center',
        'no_io_asset',
        'items_data',
        'delivery_time',
        'quality',
        'payment_terms',
        'experience_non_ippi',
        'preparer_name',
        'negotiator_name',
        'approver_name',
        'selected_vendor_name',
        'signature_data',
    ];

    protected function casts(): array
    {
        return [
            'nominal_request' => 'decimal:2',
            'approval_step'   => 'integer',
            'items_data'      => 'array',
        ];
    }

    // =========================================================
    // BOOT: Auto-generate QR Token saat create
    // =========================================================

    protected static function booted(): void
    {
        static::creating(function (ProposalHarga $ph) {
            if (empty($ph->qr_token)) {
                $ph->qr_token = Str::uuid()->toString();
            }
        });
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * PH berasal dari satu PPBJ.
     */
    public function ppbj(): BelongsTo
    {
        return $this->belongsTo(Ppbj::class, 'ppbj_id');
    }

    /**
     * PH terkait dengan satu Cost Center.
     */
    public function costCenterRelation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CostCenter::class, 'cost_center_id');
    }

    /**
     * PH yang disetujui menghasilkan satu Internal Agreement.
     */
    public function internalAgreement(): HasOne
    {
        return $this->hasOne(InternalAgreement::class, 'ph_id');
    }

    /**
     * Riwayat approval Proposal Harga (tanda tangan digital).
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(PhApproval::class, 'ph_id')->orderBy('step');
    }

    // =========================================================
    // SCOPE
    // =========================================================

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'In_Review');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    // =========================================================
    // HELPER APPROVAL WORKFLOW
    // =========================================================

    /**
     * Langkah approval berikutnya berdasarkan approval_step saat ini.
     */
    public function getNextApproverLabelAttribute(): string
    {
        return match((int) $this->approval_step) {
            1       => 'Kepala Departemen',
            2       => 'Kepala Divisi',
            3       => 'Accounting',
            4       => 'Final — Disetujui',
            default => 'Unknown',
        };
    }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    /**
     * URL QR Code untuk hybrid approval (link ke endpoint approval by token).
     */
    public function getQrUrlAttribute(): string
    {
        return route('approval.qr', ['token' => $this->qr_token]);
    }

    // =========================================================
    // STATIC HELPER: Generate PH Number
    // =========================================================

    /**
     * Generate nomor PH otomatis.
     * Format: 001/PURCH/IPPI/Jan/2024
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
        if ($lastRecord && preg_match('/^(\d{3})\//', $lastRecord->ph_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        $seqStr = str_pad($seq, 3, '0', STR_PAD_LEFT);
        return "{$seqStr}/PURCH/IPPI/{$monthRoman}/{$year}";
    }
}