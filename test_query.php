<?php

use App\Models\User;
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;

$user = User::where('email', 'kadept.acc@establish.dev')->first();
$deptId = $user->dept_id;

$iaQuery = InternalAgreement::where('status_ia', 'In_Review')
    ->where(function($q) use ($user, $deptId) {
        if ($user->isKaDept()) {
            $q->orWhere(function($q1) use ($deptId) {
                $q1->where('approval_step', 1)->whereHas('proposalHarga.ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
            });
        }
        if ($user->isKaDiv()) {
            $q->orWhere('approval_step', 2);
        }
        if ($user->isKaDeptAcc()) {
            $q->orWhere('approval_step', 3);
        }
    });

dump('IA Count:', $iaQuery->count());

$phQuery = ProposalHarga::where('status', 'In_Review')
    ->where(function($q) use ($user, $deptId) {
        if ($user->isKaDept()) {
            $q->orWhere(function($q1) use ($deptId) {
                $q1->where('approval_step', 1)->whereHas('ppbj.user', fn($q2) => $q2->where('dept_id', $deptId));
            });
        }
        if ($user->isKaDiv()) {
            $q->orWhere('approval_step', 2);
        }
        if ($user->isAccounting()) {
            $q->orWhere('approval_step', 3);
        }
    });

dump('PH Count:', $phQuery->count());
dump('PH items:', $phQuery->get()->toArray());
