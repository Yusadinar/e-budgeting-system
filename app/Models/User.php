<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'dept_id',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * User berasal dari satu department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    /**
     * User bisa membuat banyak PPBJ.
     */
    public function ppbjList(): HasMany
    {
        return $this->hasMany(Ppbj::class, 'user_id');
    }

    // =========================================================
    // HELPER ROLE CHECK
    // =========================================================

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isKaSie(): bool
    {
        return $this->role === 'ka_sie';
    }

    public function isKaDept(): bool
    {
        return in_array($this->role, ['ka_dept', 'ka_dept_acc']);
    }

    public function isKaDiv(): bool
    {
        return in_array($this->role, ['ka_div', 'ka_div_acc']);
    }

    public function isAccounting(): bool
    {
        return in_array($this->role, ['accounting', 'ka_dept_acc', 'ka_div_acc']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isKaDeptAcc(): bool
    {
        return $this->role === 'ka_dept_acc';
    }

    public function isKaDivAcc(): bool
    {
        return $this->role === 'ka_div_acc';
    }

    public function isFinDir(): bool
    {
        return $this->role === 'fin_dir';
    }

    public function isManDir(): bool
    {
        return $this->role === 'man_dir';
    }

    public function isPresDir(): bool
    {
        return $this->role === 'pres_dir';
    }

    public function isDirector(): bool
    {
        return in_array($this->role, ['fin_dir', 'man_dir', 'pres_dir']);
    }

    /**
     * Cek apakah user bisa melakukan approval.
     */
    public function canApprove(): bool
    {
        return in_array($this->role, [
            'ka_sie', 'ka_dept', 'ka_div', 'accounting',
            'ka_dept_acc', 'ka_div_acc', 'fin_dir', 'man_dir', 'pres_dir'
        ]);
    }
}