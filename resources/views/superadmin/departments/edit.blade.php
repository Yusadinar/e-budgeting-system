@extends('layouts.superadmin')
@section('title', 'Edit Departemen')
@section('page-title', 'Edit Departemen')
@section('page-subtitle', 'Perbarui informasi departemen')

@section('content')

<div class="max-w-lg animate-page">
    <a href="{{ route('superadmin.departments.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-violet-600 transition-colors mb-5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke daftar departemen
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-card">
        <h3 class="text-lg font-semibold text-slate-800 mb-6">Edit — {{ $department->dept_name }}</h3>

        <form method="POST" action="{{ route('superadmin.departments.update', $department) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label for="dept_name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Departemen</label>
                <input type="text" name="dept_name" id="dept_name" value="{{ old('dept_name', $department->dept_name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all">
                @error('dept_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="budget_code" class="block text-sm font-medium text-slate-700 mb-1.5">Kode Anggaran</label>
                <input type="text" name="budget_code" id="budget_code" value="{{ old('budget_code', $department->budget_code) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all">
                @error('budget_code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
                               text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25">
                    Perbarui Departemen
                </button>
                <a href="{{ route('superadmin.departments.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
