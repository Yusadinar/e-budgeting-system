<?php
// app/Models/Ppbj.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ppbj extends Model
{
    // Nama tabel eksplisit karena tidak mengikuti konvensi plural standar
    protected $table = 'ppbj';

    protected $fillable = [
        'user_id',
        'jenis_pengeluaran',
        'ppbj_number',
        'approval_step',
        'status',
        // Form fields baru
        'department_section',
        'subject',
        'nama_barang_jasa',
        'spesifikasi',
        'ia_no',
        'io_fr_no',
        'qty',
        'uom',
        'pernah_order',
        'pernah_order_bulan',
        'bg_what',
        'bg_why',
        'bg_when',
        'bg_where',
        'bg_who',
        'bg_how',
        'risk_analysis',
        'condition_photo',
        'spec_brand',
        'spec_maker',
        'spec_negara_asal',
        'spec_lain_lain',
        'urgency_level',
        'potensi_line_stop',
        'urgency_options',
        'pengadaan_baru_untuk',
        'schedule_general_check',
        'budget_type',
        'budget_amount_range',
        'capex_attachment',
        'layout_photo',
        'lokasi_pressline',
        'lokasi_sub_assy',
        'lokasi_metal_finish',
        'lokasi_lain_lain',
    ];

    protected function casts(): array
    {
        return [
            'qty'                    => 'integer',
            'urgency_options'        => 'array',
            'schedule_general_check' => 'date',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * PPBJ dibuat oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Satu PPBJ bisa memiliki banyak Proposal Harga.
     * (Misalnya jika vendor pertama ditolak dan diajukan ulang)
     */
    public function proposalHarga(): HasMany
    {
        return $this->hasMany(ProposalHarga::class, 'ppbj_id');
    }

    /**
     * Shortcut: Ambil PH aktif (terbaru) dari PPBJ ini.
     */
    public function latestProposalHarga(): HasOne
    {
        return $this->hasOne(ProposalHarga::class, 'ppbj_id')
                    ->latestOfMany();
    }

    // =========================================================
    // STATIC HELPER: Generate PPBJ Number
    // Format: XX/PROP/PURCH/MM/YYYY
    // =========================================================

    /**
     * Generate nomor PPBJ otomatis.
     * Format: XX/PROP/PURCH/MM/YYYY
     */
    public static function generateNumber(): string
    {
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $year  = now()->year;

        $lastRecord = static::whereYear('created_at', $year)
                            ->whereMonth('created_at', now()->month)
                            ->orderBy('id', 'desc')
                            ->first();

        $seq = 1;
        if ($lastRecord && preg_match('/^(\d+)\//', $lastRecord->ppbj_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        $seqStr = str_pad($seq, 2, '0', STR_PAD_LEFT);
        return "{$seqStr}/PROP/PURCH/{$month}/{$year}";
    }

    // =========================================================
    // ACCESSOR
    // =========================================================

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis_pengeluaran) {
            'FR'    => 'Fixed Rate',
            'IR'    => 'Internal Rate',
            'IO'    => 'Internal Order',
            default => $this->jenis_pengeluaran,
        };
    }

    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

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

    /**
     * Daftar UoM (Unit of Measure) yang tersedia.
     */
    public static function uomOptions(): array
    {
        return [
            'pcs'   => 'Pcs',
            'set'   => 'Set',
            'unit'  => 'Unit',
            'kg'    => 'Kg',
            'liter' => 'Liter',
            'meter' => 'Meter',
            'roll'  => 'Roll',
            'box'   => 'Box',
            'lot'   => 'Lot',
            'pack'  => 'Pack',
            'pair'  => 'Pair',
        ];
    }

    /**
     * Daftar range amount budget.
     */
    public static function budgetAmountRanges(): array
    {
        return [
            'under_5m'      => '< 5.000.000',
            '5m_to_10m'     => '5.000.000 < n ≤ 10.000.000',
            '10m_to_50m'    => '10.000.000 < n ≤ 50.000.000',
            '50m_to_100m'   => '50.000.000 < n ≤ 100.000.000',
            '100m_to_500m'  => '100.000.000 < n ≤ 500.000.000',
            '500m_to_1b'    => '500.000.000 < n ≤ 1.000.000.000',
            'over_1b'       => '> 1.000.000.000',
        ];
    }
}