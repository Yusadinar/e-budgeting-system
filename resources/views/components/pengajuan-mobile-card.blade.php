@props(['item'])
@php
    $ph = $item->latestProposalHarga;
    $ia = $ph?->internalAgreement;
    
    $latestDoc = 'PPBJ';
    $latestStatus = $item->status;

    if ($ia) {
        $latestDoc = 'IA';
        $latestStatus = $ia->status_ia;
    } elseif ($ph) {
        $latestDoc = 'PH';
        $latestStatus = $ph->status;
    }
@endphp
<a href="{{ route('tracking.show', $item->id) }}"
   class="block bg-white rounded-2xl border border-slate-100 shadow-card p-4 hover:border-indigo-200 transition-colors">
    <div class="flex items-start justify-between mb-2">
        <div>
            <p class="font-medium text-slate-800 text-sm">{{ $ph?->subject ?? 'Pengajuan PPBJ' }}</p>
            <p class="font-mono text-xs text-slate-400 mt-0.5">{{ $item->ppbj_number }}</p>
        </div>
        <span @class([
            'shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium',
            'bg-slate-100 text-slate-600'    => $latestStatus === 'Draft',
            'bg-amber-50 text-amber-600'     => $latestStatus === 'In_Review',
            'bg-emerald-50 text-emerald-600' => $latestStatus === 'Approved',
            'bg-rose-50 text-rose-600'       => $latestStatus === 'Rejected',
        ])>{{ $latestDoc }}: {{ str_replace('_', ' ', $latestStatus) }}</span>
    </div>
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-400">{{ $item->user->name }}</p>
    </div>
</a>
