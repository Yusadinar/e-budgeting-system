<?php
// app/Models/BudgetUpload.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetUpload extends Model
{
    protected $fillable = [
        'dept_id',
        'uploaded_by',
        'file_name',
        'outlook_number',
        'outlook_period',
        'category',
        'fiscal_year',
        'items',
        'total_amount',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'items'        => 'array',
            'total_amount' => 'decimal:2',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
