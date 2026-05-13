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
    ];

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
    // =========================================================

    /**
     * Generate nomor PPBJ otomatis.
     * Format: PPBJ-2024-0001
     */
    public static function generateNumber(): string
    {
        $year  = now()->year;
        $lastRecord = static::whereYear('created_at', $year)
                            ->orderBy('id', 'desc')
                            ->first();

        $seq = 1;
        if ($lastRecord && preg_match('/-(\d{4})$/', $lastRecord->ppbj_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        return "PPBJ-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
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
}