@extends('layouts.guest')
@section('title', 'Konfirmasi Approval QR')

@section('content')
<div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8">
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z"/>
            </svg>
        </div>
        <h1 class="text-xl font-display text-slate-900">Konfirmasi Approval</h1>
        <p class="text-slate-500 text-sm mt-1">Anda diminta menyetujui dokumen berikut</p>
    </div>

    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">No. PH</span>
            <span class="font-mono font-medium text-slate-700">{{ $ph->ph_number }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">Subjek</span>
            <span class="font-medium text-slate-700 text-right max-w-48">{{ $ph->subject }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">Nominal</span>
            <span class="font-semibold text-indigo-600">Rp {{ number_format($ph->nominal_request, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500">Pengaju</span>
            <span class="text-slate-700">{{ $ph->ppbj->user->name }}</span>
        </div>
    </div>

    <p class="text-xs text-center text-slate-400 mb-5">
        Dengan menekan tombol setuju, Anda bertanggung jawab atas keputusan approval ini.
        Silakan login untuk proses penolakan atau tindakan lanjutan.
    </p>

    <div class="space-y-2">
        <form method="POST" action="{{ route('approval.approve', $ph->id) }}">
            @csrf
            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700
                           text-white text-sm font-medium transition-all shadow-lg shadow-emerald-500/25">
                ✓ Setujui via QR
            </button>
        </form>
        <a href="{{ route('login') }}"
           class="block w-full py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium
                  text-center hover:bg-slate-50 transition-all">
            Login untuk opsi lainnya
        </a>
    </div>
</div>
@endsection