<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IaApproval extends Model
{
    protected $fillable = [
        'ia_id',
        'user_id',
        'step',
        'action',
        'signature_data',
        'reject_reason',
    ];

    public function ia()
    {
        return $this->belongsTo(InternalAgreement::class, 'ia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
