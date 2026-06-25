@extends('layouts.superadmin')
@section('title', 'Kelola Departemen')
@section('page-title', 'Departemen')
@section('page-subtitle', 'Manajemen departemen dan ringkasan anggaran')

@section('content')

{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-page">
    <div>
        <h2 class="text-lg font-display text-slate-900">Daftar Departemen</h2>
        <p class="text-sm text-slate-500 mt-0.5">{{ $departments->count() }} departemen terdaftar</p>
    </div>
    <a href="{{ route('superadmin.departments.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
              text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25 hover:shadow-violet-500/35">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Departemen
    </a>
</div>

{{-- Department Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($departments as $i => $dept)
    @php
        $plan = (float) $dept->total_plan;
        $used = (float) $dept->total_used;
        $reserved = (float) $dept->total_reserved;
        $remaining = $plan - $used - $reserved;
        $utilPct = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-all duration-300
                animate-page{{ $i < 3 ? '-delay-' . ($i + 1) : '' }}">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-50 to-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">{{ $dept->dept_name }}</h3>
                    <p class="text-[11px] text-slate-400 font-mono">{{ $dept->budget_code }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('superadmin.departments.edit', $dept) }}"
                   class="p-1.5 rounded-lg text-slate-400 hover:text-violet-600 hover:bg-violet-50 transition-all"
                   title="Edit">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('superadmin.departments.destroy', $dept) }}"
                      onsubmit="return confirm('Yakin ingin menghapus departemen {{ $dept->dept_name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                            title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Budget Summary --}}
        <div class="space-y-2 mb-4">
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Pagu Anggaran</span>
                <span class="font-semibold text-slate-700">@format_rupiah($plan)</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Terpakai</span>
                <span class="font-medium text-violet-600">@format_rupiah($used)</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Sisa</span>
                <span class="font-medium {{ $remaining < 0 ? 'text-rose-600' : 'text-emerald-600' }}">@format_rupiah($remaining)</span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mb-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] text-slate-400">Utilisasi</span>
                <span class="text-[11px] font-semibold {{ $utilPct >= 80 ? ($utilPct >= 90 ? 'text-rose-600' : 'text-amber-600') : 'text-violet-600' }}">{{ $utilPct }}%</span>
            </div>
            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700
                     {{ $utilPct >= 90 ? 'bg-rose-500' : ($utilPct >= 80 ? 'bg-amber-500' : 'bg-violet-500') }}"
                     style="width: {{ min($utilPct, 100) }}%"></div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
            <span class="text-[11px] text-slate-400">
                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                {{ $dept->users_count }} user
            </span>
            <a href="{{ route('superadmin.budget.show', $dept) }}"
               class="text-[11px] text-violet-500 hover:text-violet-700 font-medium transition-colors">
                Detail Budget →
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12">
        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/>
            </svg>
        </div>
        <p class="text-sm text-slate-400">Belum ada departemen terdaftar</p>
    </div>
    @endforelse
</div>

@endsection
