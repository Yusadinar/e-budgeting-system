<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhApproval extends Model
{
    protected $fillable = [
        'ph_id',
        'user_id',
        'step',
        'action',
        'signature_data',
        'reject_reason',
    ];

    protected function casts(): array
    {
        return [
            'step' => 'integer',
        ];
    }

    public function proposalHarga(): BelongsTo
    {
        return $this->belongsTo(ProposalHarga::class, 'ph_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
