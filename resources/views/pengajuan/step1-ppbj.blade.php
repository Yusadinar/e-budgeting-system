@extends('layouts.app')
@section('title', 'Buat Pengajuan — Step 1')
@section('page-title', 'Pengajuan Baru')
@section('page-subtitle', 'Step 1 dari 3 — Input PPBJ')

@section('content')

{{-- Stepper Header --}}
<div class="mb-6 animate-page">
    <div class="flex items-center gap-2">
        @foreach(['PPBJ', 'Proposal Harga', 'Internal Agreement'] as $i => $step)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0',
                'bg-indigo-600 text-white'   => $loop->index === 0,
                'bg-slate-100 text-slate-400' => $loop->index !== 0,
            ])>{{ $loop->iteration }}</div>
            <span @class([
                'text-xs font-medium hidden sm:block',
                'text-indigo-600' => $loop->index === 0,
                'text-slate-400'  => $loop->index !== 0,
            ])>{{ $step }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px bg-slate-200 mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-lg animate-page-delay-1">
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-6">
        <h3 class="text-base font-semibold text-slate-800 mb-1">Data PPBJ</h3>
        <p class="text-xs text-slate-400 mb-5">Pilih jenis pengeluaran untuk memulai pengajuan</p>

        <form method="POST" action="{{ route('pengajuan.store-ppbj') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-xs font-medium text-slate-600 mb-3">Jenis Pengeluaran</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['FR' => 'Fund Reservation', 'IR' => 'Internal Rate', 'IO' => 'Internal Order'] as $val => $label)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="jenis_pengeluaran" value="{{ $val }}"
                               class="peer sr-only"
                               {{ old('jenis_pengeluaran') === $val ? 'checked' : '' }} />
                        <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                    hover:border-slate-300 transition-all text-center">
                            <span class="text-sm font-bold text-slate-700 peer-checked:text-indigo-600">{{ $val }}</span>
                            <span class="text-[10px] text-slate-400 leading-tight">{{ $label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('jenis_pengeluaran')
                    <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Budget Info --}}
            @if($budget)
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 mb-5">
                <p class="text-xs font-medium text-slate-600 mb-2">Informasi Pagu Anggaran</p>
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Total Pagu</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($budget->total_plan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Terpakai</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($budget->total_used, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs border-t border-slate-200 pt-1.5">
                        <span class="font-medium text-slate-600">Sisa Pagu</span>
                        <span class="font-semibold text-emerald-600">Rp {{ number_format($budget->remaining, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25">
                Lanjut ke Proposal Harga →
            </button>
        </form>
    </div>
</div>

@endsection