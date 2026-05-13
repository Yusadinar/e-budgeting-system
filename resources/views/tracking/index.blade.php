@extends('layouts.app')
@section('title', 'Tracking Approval')
@section('page-title', 'Tracking Approval')
@section('page-subtitle', 'Pantau status semua pengajuan anggaran')

@section('content')

{{-- Filter Bar --}}
<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-5 animate-page">
    <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nomor PPBJ atau subjek..."
               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800
                      placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all" />
    </div>
    <button type="submit"
            class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25">
        Filter
    </button>
</form>

{{-- Section Pending Approvals --}}
@if(isset($pendingApprovals) && $pendingApprovals->isNotEmpty())
<div class="mb-8 animate-page">
    <div class="flex items-center gap-2 mb-4">
        <div class="bg-rose-100 p-1.5 rounded-lg">
            <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-800">Perlu Review Anda</h2>
        <span class="bg-rose-100 text-rose-700 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $pendingApprovals->count() }}</span>
    </div>

    {{-- Desktop Table (Pending) --}}
    <div class="hidden sm:block bg-rose-50/30 rounded-2xl border-2 border-rose-200 shadow-sm overflow-hidden mb-4 relative">
        <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
        <table class="w-full text-sm relative z-10">
            <thead>
                <tr class="border-b border-rose-100 bg-rose-50/50">
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-rose-800 uppercase tracking-wide">Pengajuan (Action Required)</th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-rose-800 uppercase tracking-wide">Pengaju</th>
                    <th class="px-5 py-3.5 text-left text-xs font-bold text-rose-800 uppercase tracking-wide">Status Terkini</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-100">
                @foreach($pendingApprovals as $item)
                    <x-pengajuan-table-row :item="$item" />
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Card List (Pending) --}}
    <div class="sm:hidden space-y-3 mb-4">
        @foreach($pendingApprovals as $item)
            <div class="border-2 border-rose-300 rounded-2xl shadow-sm overflow-hidden relative">
                <div class="absolute top-0 left-0 w-1 h-full bg-rose-500 z-10"></div>
                <x-pengajuan-mobile-card :item="$item" />
            </div>
        @endforeach
    </div>
</div>
@endif

@php
    $mainTitle = in_array($user->role, ['staff', 'ka_dept']) ? 'Pengajuan Anda / Departemen' : 'Semua Pengajuan';
@endphp

@if(isset($pendingApprovals) && $pendingApprovals->isNotEmpty())
<div class="flex items-center justify-between mb-4 animate-page-delay-1 mt-6 border-t border-slate-100 pt-6">
    <h2 class="text-lg font-bold text-slate-800">{{ $mainTitle }}</h2>
</div>
@else
<div class="flex items-center justify-between mb-4 animate-page-delay-1">
    <h2 class="text-lg font-bold text-slate-800">{{ $mainTitle }}</h2>
</div>
@endif

{{-- Desktop Table (Main) --}}
<div class="hidden sm:block bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-1">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 bg-slate-50/60">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengajuan</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengaju</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status Terkini</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($pengajuan as $item)
                <x-pengajuan-table-row :item="$item" />
            @empty
            <tr>
                <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                    <p class="text-sm font-medium">Tidak ada pengajuan ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($pengajuan->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">
        {{ $pengajuan->links() }}
    </div>
    @endif
</div>

{{-- Mobile Card List (Main) --}}
<div class="sm:hidden space-y-3 mt-4 animate-page-delay-1">
    @forelse($pengajuan as $item)
        <x-pengajuan-mobile-card :item="$item" />
    @empty
    <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center text-slate-400">
        <p class="text-sm font-medium">Tidak ada pengajuan ditemukan</p>
    </div>
    @endforelse
</div>

{{-- Section Ajuan Orang Lain --}}
@if(isset($otherPengajuan) && $otherPengajuan->total() > 0)
<div class="flex items-center gap-2 mb-4 animate-page-delay-1 mt-10 border-t border-slate-100 pt-6">
    <h2 class="text-lg font-bold text-slate-800">Ajuan Orang Lain</h2>
    <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $otherPengajuan->total() }}</span>
</div>

{{-- Desktop Table (Other) --}}
<div class="hidden sm:block bg-slate-50/50 rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-page-delay-1">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-100/50">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengajuan</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengaju</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status Terkini</th>
                <th class="px-5 py-3.5"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach($otherPengajuan as $item)
                <x-pengajuan-table-row :item="$item" />
            @endforeach
        </tbody>
    </table>
    @if($otherPengajuan->hasPages())
    <div class="px-5 py-3 border-t border-slate-200">
        {{ $otherPengajuan->links() }}
    </div>
    @endif
</div>

{{-- Mobile Card List (Other) --}}
<div class="sm:hidden space-y-3 mt-4 animate-page-delay-1">
    @foreach($otherPengajuan as $item)
        <div class="opacity-80 hover:opacity-100 transition-opacity">
            <x-pengajuan-mobile-card :item="$item" />
        </div>
    @endforeach
</div>
@endif

@endsection