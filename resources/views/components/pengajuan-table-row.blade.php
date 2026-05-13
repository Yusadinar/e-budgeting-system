@props(['item'])
@php 
    $ph = $item->latestProposalHarga;
    $ia = $ph?->internalAgreement;
@endphp
<tr class="hover:bg-slate-50/50 transition-colors">
    <td class="px-5 py-4">
        <span class="font-mono text-xs text-slate-600 block">{{ $item->ppbj_number }}</span>
        <span class="text-slate-700 font-medium block mt-1">{{ $ph?->subject ?? 'Belum ada PH' }}</span>
    </td>
    <td class="px-5 py-4">
        <p class="text-slate-600 text-xs">{{ $item->user->name }}</p>
        <p class="text-slate-400 text-[11px]">{{ $item->user->department?->dept_name }}</p>
    </td>
    <td class="px-5 py-4">
        <div class="flex flex-col gap-1 items-start">
            <span @class([
                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                'bg-slate-100 text-slate-600'   => $item->status === 'Draft',
                'bg-amber-50 text-amber-600'    => $item->status === 'In_Review',
                'bg-emerald-50 text-emerald-600'=> $item->status === 'Approved',
                'bg-rose-50 text-rose-600'      => $item->status === 'Rejected',
            ])>PPBJ: {{ str_replace('_', ' ', $item->status) }}</span>

            @if($ph)
            <span @class([
                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                'bg-slate-100 text-slate-600'   => $ph->status === 'Draft',
                'bg-amber-50 text-amber-600'    => $ph->status === 'In_Review',
                'bg-emerald-50 text-emerald-600'=> $ph->status === 'Approved',
                'bg-rose-50 text-rose-600'      => $ph->status === 'Rejected',
            ])>PH: {{ str_replace('_', ' ', $ph->status) }}</span>
            @endif

            @if($ia)
            <span @class([
                'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium',
                'bg-slate-100 text-slate-600'   => $ia->status_ia === 'Draft',
                'bg-amber-50 text-amber-600'    => $ia->status_ia === 'In_Review',
                'bg-emerald-50 text-emerald-600'=> $ia->status_ia === 'Approved',
                'bg-rose-50 text-rose-600'      => $ia->status_ia === 'Rejected',
            ])>IA: {{ str_replace('_', ' ', $ia->status_ia) }}</span>
            @endif
        </div>
    </td>
    <td class="px-5 py-4 text-right">
        <a href="{{ route('tracking.show', $item->id) }}"
           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            Detail &rarr;
        </a>
    </td>
</tr>
