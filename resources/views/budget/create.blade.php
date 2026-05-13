@extends('layouts.app')
@section('title', 'Input Budget Anggaran')
@section('page-title', 'Input Budget Anggaran')
@section('page-subtitle', 'Penetapan atau penambahan pagu anggaran departemen')

@section('content')

<div class="max-w-1xl animate-page">

    {{-- Info Department --}}
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl border border-indigo-200 p-5 mb-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Departemen</p>
                <p class="text-lg font-bold text-indigo-900 mt-0.5">{{ $department->dept_name }}</p>
                <p class="text-xs text-indigo-600 mt-1">Kode Budget: <span class="font-mono font-semibold">{{ $department->budget_code }}</span></p>
            </div>
        </div>

        @if($budget)
        <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-indigo-200">
            <div>
                <p class="text-[10px] text-indigo-600 uppercase tracking-wide font-semibold">Total Pagu</p>
                <p class="text-sm font-bold text-indigo-900 mt-0.5">Rp {{ number_format($budget->total_plan, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-indigo-600 uppercase tracking-wide font-semibold">Terpakai</p>
                <p class="text-sm font-bold text-indigo-900 mt-0.5">Rp {{ number_format($budget->total_used, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-indigo-600 uppercase tracking-wide font-semibold">Sisa</p>
                <p class="text-sm font-bold text-emerald-600 mt-0.5">Rp {{ number_format($budget->remaining, 0, ',', '.') }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Form Input Budget --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 animate-page-delay-1">
        <h3 class="text-base font-semibold text-slate-800 mb-1">Form Input Budget</h3>
        <p class="text-xs text-slate-400 mb-5">Isi data anggaran untuk tahun fiskal terkait</p>

        <form method="POST" action="{{ route('budget.store') }}">
            @csrf

            {{-- Fiscal Year --}}
            <div class="mb-4">
                <label for="fiscal_year" class="block text-xs font-medium text-slate-600 mb-1.5">Tahun Fiskal</label>
                <select id="fiscal_year" name="fiscal_year" required
                        class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                               border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                               transition-all outline-none @error('fiscal_year') border-rose-400 @enderror">
                    @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @error('fiscal_year') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Tipe Adjustment --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-2">Tipe Input</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="adjustment_type" value="new" class="peer sr-only" {{ old('adjustment_type', 'new') === 'new' ? 'checked' : '' }} />
                        <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                    hover:border-slate-300 transition-all text-center">
                            <svg class="w-5 h-5 text-slate-400 peer-checked:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-xs font-semibold text-slate-700">Set Baru</span>
                            <span class="text-[10px] text-slate-400">Ganti total pagu</span>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" name="adjustment_type" value="increase" class="peer sr-only" {{ old('adjustment_type') === 'increase' ? 'checked' : '' }} />
                        <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50
                                    hover:border-slate-300 transition-all text-center">
                            <svg class="w-5 h-5 text-slate-400 peer-checked:text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="text-xs font-semibold text-slate-700">Tambah Pagu</span>
                            <span class="text-[10px] text-slate-400">Increment nominal</span>
                        </div>
                    </label>
                </div>
                @error('adjustment_type') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Total Plan --}}
            <div class="mb-4">
                <label for="total_plan" class="block text-xs font-medium text-slate-600 mb-1.5">
                    Nominal (Rp)
                    <span id="label-hint" class="text-slate-400 font-normal"></span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium">Rp</span>
                    <input id="total_plan" type="number" name="total_plan" value="{{ old('total_plan') }}" required min="1" step="1"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none @error('total_plan') border-rose-400 @enderror"
                           placeholder="0" />
                </div>
                <p class="mt-1.5 text-xs text-slate-400">Masukkan dalam satuan Rupiah penuh (tanpa koma/titik)</p>
                @error('total_plan') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-5">
                <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">
                    Catatan / Keterangan
                    <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                                 border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                 transition-all outline-none resize-none"
                          placeholder="Misal: Penambahan budget Q3 untuk ekspansi IT infrastructure...">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium text-center hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700
                               text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25">
                    Simpan Budget
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
const typeRadios = document.querySelectorAll('input[name="adjustment_type"]');
const labelHint  = document.getElementById('label-hint');

typeRadios.forEach(radio => {
    radio.addEventListener('change', (e) => {
        if (e.target.value === 'new') {
            labelHint.textContent = '— Total pagu baru';
        } else {
            labelHint.textContent = '— Nominal yang ditambahkan';
        }
    });
});

// Trigger awal
document.querySelector('input[name="adjustment_type"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endpush