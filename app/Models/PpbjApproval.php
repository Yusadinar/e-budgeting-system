<?php
// app/Models/PpbjApproval.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpbjApproval extends Model
{
    protected $fillable = [
        'ppbj_id',
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

    public function ppbj(): BelongsTo
    {
        return $this->belongsTo(Ppbj::class, 'ppbj_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
