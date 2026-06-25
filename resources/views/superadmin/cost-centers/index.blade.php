@extends('layouts.superadmin')
@section('title', 'Kelola Cost Center')
@section('page-title', 'Cost Center')
@section('page-subtitle', 'Master Data Cost Center berdasarkan hierarki SAP')

@section('content')

{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-page">
    <div>
        <h2 class="text-lg font-display text-slate-900">Daftar Cost Center</h2>
        <p class="text-sm text-slate-500 mt-0.5">{{ $totalCostCenters }} cost center terdaftar</p>
    </div>
    <a href="{{ route('superadmin.cost-centers.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
              text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25 hover:shadow-violet-500/35">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Cost Center
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-slate-100 p-4 mb-6 shadow-sm animate-page-delay-1">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau kode..."
                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Plant</label>
            <select name="plant" class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 outline-none">
                <option value="">Semua Plant</option>
                @foreach($plants as $p)
                    <option value="{{ $p }}" {{ request('plant') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
            <select name="expense_type" class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 outline-none">
                <option value="">Semua</option>
                <option value="FOH" {{ request('expense_type') === 'FOH' ? 'selected' : '' }}>FOH</option>
                <option value="OPEX" {{ request('expense_type') === 'OPEX' ? 'selected' : '' }}>OPEX</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Dept. Group</label>
            <select name="department_group" class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 outline-none">
                <option value="">Semua</option>
                @foreach($deptGroups as $dg)
                    <option value="{{ $dg }}" {{ request('department_group') === $dg ? 'selected' : '' }}>{{ $dg }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['search', 'plant', 'expense_type', 'department_group']))
            <a href="{{ route('superadmin.cost-centers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden animate-page-delay-2">
    <div class="overflow-x-auto pb-2">
        <table class="w-full text-left border-collapse whitespace-nowrap min-w-[1000px]">
            <thead>
                <tr class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider">
                    <th class="p-3 font-semibold rounded-tl-lg">#</th>
                    <th class="p-3 font-semibold">Dept. Group</th>
                    <th class="p-3 font-semibold">Plant</th>
                    <th class="p-3 font-semibold">Type</th>
                    <th class="p-3 font-semibold">Kode CC</th>
                    <th class="p-3 font-semibold">Nama Cost Center</th>
                    <th class="p-3 font-semibold text-right">Pagu</th>
                    <th class="p-3 font-semibold text-right">Terpakai</th>
                    <th class="p-3 font-semibold text-right">Sisa</th>
                    <th class="p-3 font-semibold text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($costCenters as $i => $cc)
                @php
                    $budget = $cc->annualBudgets->where('fiscal_year', now()->year)->first();
                    $plan = (float) ($budget?->total_plan ?? 0);
                    $used = (float) ($budget?->total_used ?? 0);
                    $reserved = (float) ($budget?->total_reserved ?? 0);
                    $remaining = $plan - $used - $reserved;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-3 text-slate-400">{{ $costCenters->firstItem() + $i }}</td>
                    <td class="p-3 font-medium text-slate-700">{{ $cc->department_group }}</td>
                    <td class="p-3">
                        <span @class([
                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                            'bg-blue-100 text-blue-800' => $cc->plant === 'IBEK',
                            'bg-emerald-100 text-emerald-800' => $cc->plant === 'IKAR',
                            'bg-amber-100 text-amber-800' => $cc->plant === 'HO',
                        ])>{{ $cc->plant }}</span>
                    </td>
                    <td class="p-3">
                        <span @class([
                            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                            'bg-indigo-100 text-indigo-800' => $cc->expense_type === 'FOH',
                            'bg-violet-100 text-violet-800' => $cc->expense_type === 'OPEX',
                        ])>{{ $cc->expense_type }}</span>
                    </td>
                    <td class="p-3 font-mono text-xs text-slate-600">{{ $cc->cost_center_code ?? '—' }}</td>
                    <td class="p-3 font-medium text-slate-800">{{ $cc->cost_center_name }}</td>
                    <td class="p-3 text-right text-slate-600">Rp {{ number_format($plan, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-indigo-600 font-medium">Rp {{ number_format($used, 0, ',', '.') }}</td>
                    <td class="p-3 text-right font-medium {{ $remaining < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        Rp {{ number_format($remaining, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('superadmin.cost-centers.edit', $cc) }}"
                               class="p-1.5 rounded-lg text-slate-400 hover:text-violet-600 hover:bg-violet-50 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.cost-centers.destroy', $cc) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus cost center {{ $cc->cost_center_name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="p-8 text-center text-sm text-slate-400">
                        Belum ada cost center terdaftar. Silakan jalankan seeder atau tambah manual.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($costCenters->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $costCenters->links() }}
    </div>
    @endif
</div>

@endsection
