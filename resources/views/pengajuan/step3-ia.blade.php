@extends('layouts.app')
@section('title', 'Buat Pengajuan — Step 3')
@section('page-title', 'Pengajuan Baru')
@section('page-subtitle', 'Step 3 dari 3 — Internal Agreement')

@section('content')

{{-- Stepper Header --}}
<div class="mb-6 animate-page">
    <div class="flex items-center gap-2">
        @foreach(['PPBJ', 'Proposal Harga', 'Internal Agreement'] as $i => $step)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0',
                'bg-indigo-600 text-white'    => true,
                'ring-2 ring-indigo-200'      => $loop->last,
            ])>
                @if(!$loop->last)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                @else
                {{ $loop->iteration }}
                @endif
            </div>
            <span class="text-xs font-medium hidden sm:block {{ $loop->last ? 'text-indigo-600' : 'text-slate-400' }}">{{ $step }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px bg-indigo-300 mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-lg animate-page-delay-1">
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-6">
        {{-- Ringkasan PH --}}
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 mb-5">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-2">Ringkasan Proposal Harga</p>
            <p class="text-sm font-medium text-slate-700">{{ $proposalHarga->subject }}</p>
            <div class="flex items-center gap-3 mt-1.5">
                <span class="font-mono text-xs text-slate-400">{{ $proposalHarga->ph_number }}</span>
                <span class="text-xs text-slate-400">·</span>
                <span class="text-sm font-semibold text-indigo-600">Rp {{ number_format($proposalHarga->nominal_request, 0, ',', '.') }}</span>
            </div>
        </div>

        <h3 class="text-base font-semibold text-slate-800 mb-1">Internal Agreement</h3>
        <p class="text-xs text-slate-400 mb-5">Isi nominal final dan nomor SAP (opsional)</p>

        <form method="POST" action="{{ route('pengajuan.store-ia', $proposalHarga->id) }}">
            @csrf

            {{-- Final Nominal --}}
            <div class="mb-4">
                <label for="final_nominal" class="block text-xs font-medium text-slate-600 mb-1.5">Nominal Final IA (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium">Rp</span>
                    <input id="final_nominal" type="number" name="final_nominal"
                           value="{{ old('final_nominal', $proposalHarga->nominal_request) }}"
                           required min="1"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none @error('final_nominal') border-rose-400 @enderror" />
                </div>
                <p class="mt-1.5 text-xs text-slate-400">Boleh berbeda dari nominal PH (negosiasi)</p>
                @error('final_nominal') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- SAP Doc No --}}
            <div class="mb-5">
                <label for="sap_doc_no" class="block text-xs font-medium text-slate-600 mb-1.5">
                    Nomor Dokumen SAP
                    <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input id="sap_doc_no" type="text" name="sap_doc_no" value="{{ old('sap_doc_no') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                              border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                              transition-all outline-none font-mono"
                       placeholder="Contoh: 5100012345" />
                <p class="mt-1.5 text-xs text-slate-400">Diisi setelah SAP posting, bisa dilengkapi kemudian</p>
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25">
                Kirim ke Proses Review ✓
            </button>
        </form>
    </div>
</div>

@endsection