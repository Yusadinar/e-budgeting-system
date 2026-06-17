@extends('layouts.superadmin')
@section('title', 'Edit Cost Center')
@section('page-title', 'Edit Cost Center')
@section('page-subtitle', 'Perbarui data cost center: {{ $costCenter->cost_center_name }}')

@section('content')
<div class="max-w-2xl mx-auto animate-page">
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
        <form method="POST" action="{{ route('superadmin.cost-centers.update', $costCenter) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Department Group --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Department Group <span class="text-rose-500">*</span></label>
                    <input type="text" name="department_group" value="{{ old('department_group', $costCenter->department_group) }}" required
                           list="dept-groups" placeholder="Contoh: Manufacturing"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <datalist id="dept-groups">
                        @foreach($deptGroups as $dg)
                            <option value="{{ $dg }}">
                        @endforeach
                    </datalist>
                    @error('department_group') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Plant --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Plant <span class="text-rose-500">*</span></label>
                    <select name="plant" required class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <option value="IBEK" {{ old('plant', $costCenter->plant) === 'IBEK' ? 'selected' : '' }}>IBEK (Bekasi)</option>
                        <option value="IKAR" {{ old('plant', $costCenter->plant) === 'IKAR' ? 'selected' : '' }}>IKAR (Karawang)</option>
                        <option value="HO" {{ old('plant', $costCenter->plant) === 'HO' ? 'selected' : '' }}>HO (Head Office)</option>
                    </select>
                    @error('plant') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Expense Type --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Expense Type <span class="text-rose-500">*</span></label>
                    <select name="expense_type" required class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        <option value="FOH" {{ old('expense_type', $costCenter->expense_type) === 'FOH' ? 'selected' : '' }}>FOH (Factory Overhead)</option>
                        <option value="OPEX" {{ old('expense_type', $costCenter->expense_type) === 'OPEX' ? 'selected' : '' }}>OPEX (Operational Expenditure)</option>
                    </select>
                    @error('expense_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cost Center Code --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kode Cost Center</label>
                    <input type="text" name="cost_center_code" value="{{ old('cost_center_code', $costCenter->cost_center_code) }}"
                           placeholder="Contoh: P-902-3711"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('cost_center_code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Cost Center Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nama Cost Center <span class="text-rose-500">*</span></label>
                    <input type="text" name="cost_center_name" value="{{ old('cost_center_name', $costCenter->cost_center_name) }}" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('cost_center_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Budget Plan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Pagu Anggaran {{ now()->year }} (Rp)</label>
                    <input type="number" name="budget_plan" value="{{ old('budget_plan', $budget?->total_plan ?? 0) }}" min="0"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @if($budget)
                        <p class="text-[11px] text-slate-400 mt-1">
                            Terpakai: Rp {{ number_format($budget->total_used, 0, ',', '.') }} · 
                            Di-hold: Rp {{ number_format($budget->total_reserved, 0, ',', '.') }} · 
                            Sisa: Rp {{ number_format($budget->remaining, 0, ',', '.') }}
                        </p>
                    @endif
                    @error('budget_plan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('superadmin.cost-centers.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-all shadow-lg shadow-indigo-500/25">
                    Perbarui Cost Center
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
