@extends('layouts.app')
@section('title', 'Buat Pengajuan — Step 2')
@section('page-title', 'Pengajuan Baru')
@section('page-subtitle', 'Step 2 dari 3 — Proposal Harga')

@section('content')

{{-- Stepper Header --}}
<div class="mb-6 animate-page">
    <div class="flex items-center gap-2">
        @foreach(['PPBJ', 'Proposal Harga', 'Internal Agreement'] as $i => $step)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0',
                'bg-indigo-600 text-white'    => $loop->index <= 1,
                'bg-slate-100 text-slate-400' => $loop->index > 1,
            ])>
                @if($loop->index === 0)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                @else
                {{ $loop->iteration }}
                @endif
            </div>
            <span @class([
                'text-xs font-medium hidden sm:block',
                'text-indigo-600' => $loop->index === 1,
                'text-slate-400'  => $loop->index !== 1,
            ])>{{ $step }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px {{ $loop->index === 0 ? 'bg-indigo-300' : 'bg-slate-200' }} mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-lg animate-page-delay-1">
    <div class="bg-white rounded-1xl border border-slate-100 shadow-card p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-mono text-slate-600">{{ $ppbj->ppbj_number }}</span>
            <span class="text-xs text-slate-400">·</span>
            <span class="text-xs text-slate-500">{{ $ppbj->jenis_label }}</span>
        </div>

        <h3 class="text-base font-semibold text-slate-800 mb-1">Proposal Harga</h3>
        <p class="text-xs text-slate-400 mb-5">Isi detail proposal harga untuk pengajuan ini</p>

        <form method="POST" action="{{ route('pengajuan.store-ph', $ppbj->id) }}" id="form-ph">
            @csrf

            {{-- Subjek --}}
            <div class="mb-4">
                <label for="subject" class="block text-xs font-medium text-slate-600 mb-1.5">Subjek Pengajuan</label>
                <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                              border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                              transition-all outline-none @error('subject') border-rose-400 @enderror"
                       placeholder="Contoh: Pengadaan Laptop IT Department" />
                @error('subject') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Nominal --}}
            <div class="mb-5">
                <label for="nominal_request" class="block text-xs font-medium text-slate-600 mb-1.5">Nominal Pengajuan (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium">Rp</span>
                    <input id="nominal_request" type="number" name="nominal_request"
                           value="{{ old('nominal_request') }}" required min="1"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none @error('nominal_request') border-rose-400 @enderror"
                           placeholder="0"
                           oninput="validateNominal(this.value)" />
                </div>
                @error('nominal_request') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror

                {{-- Warning jika melebihi budget --}}
                @if($budget)
                <div id="budget-warning" class="hidden mt-2 flex items-center gap-2 px-3 py-2 rounded-lg bg-rose-50 border border-rose-200">
                    <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <p class="text-xs text-rose-600">Nominal melebihi sisa pagu (Rp {{ number_format($budget->remaining, 0, ',', '.') }})</p>
                </div>
                <p class="mt-1.5 text-xs text-slate-400">Sisa pagu: <strong class="text-emerald-600">Rp {{ number_format($budget->remaining, 0, ',', '.') }}</strong></p>
                @endif
            </div>

            <button type="submit" id="btn-submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25
                           disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                Lanjut ke Internal Agreement →
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const sisaPagu = {{ $budget?->remaining ?? 0 }};
const btnSubmit = document.getElementById('btn-submit');
const warning   = document.getElementById('budget-warning');

function validateNominal(val) {
    const nominal = parseFloat(val) || 0;
    const melebihi = sisaPagu > 0 && nominal > sisaPagu;
    btnSubmit.disabled = melebihi;
    if (warning) warning.classList.toggle('hidden', !melebihi);
}
</script>
@endpush