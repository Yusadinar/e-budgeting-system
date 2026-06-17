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
        'cost_center_id',
        'section',
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

    /**
     * Get username from email prefix
     */
    public function getUsernameAttribute(): string
    {
        return explode('@', $this->email)[0];
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
     * User di-assign ke satu cost center (opsional).
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CostCenter::class, 'cost_center_id');
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
        return $this->role === 'ka_dept';
    }

    public function isKaDiv(): bool
    {
        return $this->role === 'ka_div';
    }

    public function isAccounting(): bool
    {
        return $this->role === 'accounting';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isFinDir(): bool
    {
        return $this->role === 'fin_dir';
    }

    public function isManDir(): bool
    {
        return $this->role === 'man_dir';
    }

    public function isProdDir(): bool
    {
        return $this->role === 'prod_dir';
    }

    public function isPresDir(): bool
    {
        return $this->role === 'pres_dir';
    }

    public function isDirector(): bool
    {
        return in_array($this->role, ['fin_dir', 'man_dir', 'prod_dir', 'pres_dir']);
    }

    /**
     * Cek apakah user bisa melakukan approval.
     */
    public function canApprove(): bool
    {
        return in_array($this->role, [
            'ka_sie', 'ka_dept', 'ka_div', 'accounting',
            'fin_dir', 'man_dir', 'prod_dir', 'pres_dir'
        ]);
    }

    /**
     * Label jabatan yang mudah dibaca.
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->role === 'ka_sie') {
            return $this->section ? 'Kepala Seksi ' . $this->section : 'Kepala Seksi';
        }

        return match($this->role) {
            'staff'     => 'Staff',
            'ka_dept'   => 'Kepala Departemen',
            'ka_div'    => 'Kepala Divisi',
            'accounting'=> 'Accounting',
            'superadmin'=> 'Super Admin',
            'fin_dir'   => 'Finance Director',
            'man_dir'   => 'Manufacture Director',
            'prod_dir'  => 'Production Director',
            'pres_dir'  => 'President Director',
            default     => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Dapatkan daftar ID PPBJ yang sedang menunggu tindakan (Approval atau Buat Dokumen) dari user ini.
     */
    public function getPendingActionPpbjIds(): \Illuminate\Support\Collection
    {
        if (!$this->canApprove() && $this->username !== 'bramansyah.badar' && $this->username !== 'susan.anggraeni') {
            return collect([]);
        }

        $deptId = $this->dept_id;
        $isFinAcc = $this->department && str_contains(strtolower($this->department->dept_name), 'finance accounting');
        $isKaSiePurchasing = $this->isKaSie() && $this->section === 'Purchasing & Import';

        // 1. PPBJ Pending Approval
        $pendingPpbjIds = \App\Models\Ppbj::where('status', 'In_Review')
            ->where(function($q) use ($deptId, $isFinAcc, $isKaSiePurchasing) {
                $hasCondition = false;
                if ($this->isKaDept()) {
                    $q->orWhere(function($q1) use ($deptId) {
                        $q1->where('approval_step', 1)->whereHas('user', fn($q2) => $q2->where('dept_id', $deptId));
                    });
                    $hasCondition = true;
                }
                if ($isKaSiePurchasing) { $q->orWhere('approval_step', 2); $hasCondition = true; }
                if ($this->isKaDiv() && $isFinAcc) { $q->orWhere('approval_step', 3); $hasCondition = true; }
                if ($this->isFinDir()) { $q->orWhere('approval_step', 4); $hasCondition = true; }
                if ($this->isPresDir()) { $q->orWhere('approval_step', 5); $hasCondition = true; }
                if (!$hasCondition) $q->where('id', 0);
            })->pluck('id');

        // 2. PH Pending Approval
        $pendingPhPpbjIds = \App\Models\ProposalHarga::where('status', 'In_Review')
            ->where(function($q) {
                $hasCondition = false;
                if ($this->username === 'bramansyah.badar') { $q->orWhere('approval_step', 1); $hasCondition = true; }
                if ($this->username === 'fauzan.nurdinsyah') { $q->orWhere('approval_step', 2); $hasCondition = true; }
                if ($this->username === 'fadillah.ahmad') { $q->orWhere('approval_step', 3); $hasCondition = true; }
                if ($this->username === 'budiwijayanti.riana') { $q->orWhere('approval_step', 4); $hasCondition = true; }
                if ($this->username === 'riana.budiwijayanti') { $q->orWhere('approval_step', 5); $hasCondition = true; }
                if ($this->username === 'yoga.dina') { $q->orWhere('approval_step', 6); $hasCondition = true; }
                if (!$hasCondition) $q->where('id', 0);
            })->pluck('ppbj_id');

        // 3. IA Pending Approval
        $pendingIaPpbjIds = \App\Models\InternalAgreement::where('status_ia', 'In_Review')
            ->where(function($q) use ($deptId, $isFinAcc) {
                $hasCondition = false;
                if ($this->isKaDept()) {
                    $q->orWhere(function($q1) use ($deptId) {
                        $q1->where('approval_step', 1)->whereHas('proposalHarga.ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
                    });
                    if ($isFinAcc) { $q->orWhere('approval_step', 3); }
                    $hasCondition = true;
                }
                if ($this->isKaDiv()) {
                    $q->orWhere(function($q1) use ($deptId) {
                        $q1->where('approval_step', 2)->whereHas('proposalHarga.ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
                    });
                    if ($isFinAcc) { $q->orWhere('approval_step', 4); }
                    $hasCondition = true;
                }
                if ($this->isProdDir() || $this->isManDir() || $this->isPresDir()) {
                    $q->orWhere('approval_step', 5);
                    $hasCondition = true;
                }
                if ($this->isFinDir()) {
                    $q->orWhere('approval_step', 6);
                    $hasCondition = true;
                }
                if (!$hasCondition) $q->where('id', 0);
            })->with('proposalHarga')->get()->pluck('proposalHarga.ppbj_id')->filter();

        // 4. Task: Buat PH
        $pendingCreatePhPpbjIds = collect([]);
        if ($this->username === 'bramansyah.badar' || $isKaSiePurchasing) {
            $pendingCreatePhPpbjIds = \App\Models\Ppbj::where('status', 'Approved')
                ->whereDoesntHave('proposalHarga')->pluck('id');
        }

        // 5. Task: Buat IA
        $pendingCreateIaPpbjIds = collect([]);
        if ($this->username === 'susan.anggraeni' || $this->section === 'Budget & Sistem Informasi') {
            $pendingCreateIaPpbjIds = \App\Models\ProposalHarga::where('status', 'Approved')
                ->whereDoesntHave('internalAgreement')->pluck('ppbj_id');
        }

        return $pendingPpbjIds->merge($pendingPhPpbjIds)->merge($pendingIaPpbjIds)
            ->merge($pendingCreatePhPpbjIds)->merge($pendingCreateIaPpbjIds)->unique();
    }
}