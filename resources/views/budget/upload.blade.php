@extends('layouts.app')
@section('title', 'Upload Budget Excel')
@section('page-title', 'Upload Budget Excel')
@section('page-subtitle', 'Upload template Lampiran Budget dari Dept. Accounting')

@push('styles')
<style>
    .drop-zone {
        border: 2px dashed #c7d2fe;
        transition: border-color 0.2s, background 0.2s;
    }
    .drop-zone.dragover {
        border-color: #6366f1;
        background: #eef2ff;
    }
    .upload-card {
        background-color: #4f46e5; /* Solid Indigo-600 */
    }
    .ol-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    .category-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .chip-capex { background: #fef3c7; color: #92400e; }
    .chip-foh   { background: #dcfce7; color: #166534; }
    .chip-opex  { background: #dbeafe; color: #1e40af; }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
    }
    .status-confirmed { background: #dcfce7; color: #166534; }
    .status-draft     { background: #fef9c3; color: #713f12; }
</style>
@endpush

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- ── HERO CARD ──────────────────────────────── --}}
    <div class="upload-card rounded-2xl p-6 text-white shadow-xl shadow-indigo-500/20 animate-page">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-bold leading-tight">Upload Lampiran Budget</h2>
                <p class="text-indigo-100 text-sm mt-1">Upload template Excel dari Dept. Accounting (CAPEX / FOH / OPEX) untuk proses dokumentasi budget.</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg> Format: .xlsx / .xls</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg> Max 5 MB</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg> {{ $dept->dept_name }}</span>
                    @if(isset($costCenters) && $costCenters->count())
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg> {{ $costCenters->count() }} Cost Center</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── FORM UPLOAD ─────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 animate-page-delay-1">

        <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <div class="w-5 h-5 rounded-md bg-indigo-100 flex items-center justify-center">
                <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.47 1.72a.75.75 0 011.06 0l3 3a.75.75 0 01-1.06 1.06l-1.72-1.72V7.5h-1.5V4.06L9.53 5.78a.75.75 0 01-1.06-1.06l3-3zM11.25 7.5V15a.75.75 0 001.5 0V7.5h3.75a3 3 0 013 3v9a3 3 0 01-3 3h-9a3 3 0 01-3-3v-9a3 3 0 013-3h3.75z"/>
                </svg>
            </div>
            Pilih File Excel Template
        </h3>

        <form method="POST" action="{{ route('budget.upload.parse') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf

            {{-- Drop Zone --}}
            <div id="dropZone" class="drop-zone rounded-xl p-8 text-center cursor-pointer hover:bg-indigo-50/50 transition-all"
                 onclick="document.getElementById('excel_file').click()">
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" class="hidden" onchange="onFileSelected(this)" />

                <div id="dropIcon" class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 rounded-xl bg-indigo-50 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Klik atau seret file Excel ke sini</p>
                        <p class="text-xs text-slate-400 mt-0.5">Format: .xlsx atau .xls — Maks. 5 MB</p>
                    </div>
                </div>

                <div id="fileInfo" class="hidden flex-col items-center gap-2">
                    <div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p id="fileName" class="text-sm font-semibold text-slate-700"></p>
                        <p id="fileSize" class="text-xs text-slate-400 mt-0.5"></p>
                    </div>
                </div>
            </div>

            @error('excel_file')
            <p class="mt-2 text-xs text-rose-500 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z"/></svg>
                {{ $message }}
            </p>
            @enderror

            <div class="mt-4 flex flex-wrap gap-2">
                {{-- Download Template FOH --}}
                <a href="{{ route('budget.upload.download-template', ['type' => 'FOH']) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Template FOH
                    </a>

                    {{-- Download Template OPEX --}}
                    <a href="{{ route('budget.upload.download-template', ['type' => 'OPEX']) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Template OPEX
                    </a>
                    {{-- Download Template Dummy (Untuk Testing) --}}
                    <a href="{{ route('budget.upload.download-template', ['type' => 'FOH', 'dummy' => '1']) }}"
                       class="inline-flex items-center justify-center sm:justify-start gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm w-full sm:w-auto sm:ml-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3 12l3-3m0 0l3 3m-3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        Dummy Data (Test)
                    </a>
                <p class="text-[10px] text-slate-500 leading-relaxed mt-2">
                    <svg class="w-3 h-3 inline-block mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    Template <strong>FOH</strong> dan <strong>OPEX</strong> sudah otomatis berisi cost center milik <strong>{{ $dept->dept_name }}</strong>.
                </p>
            </div>

            {{-- Panduan OL (Cost Center Based) --}}
            {{-- Panduan OL (Cost Center Based) --}}
            @php
                $today = now();
                $fy = $today->year;
                $olSchedules = \App\Http\Controllers\BudgetUploadController::getOlSchedules($fy);
                
                // Tentukan Masa Upload Terdekat (Next Deadline)
                $nextUploadOl = \App\Http\Controllers\BudgetUploadController::detectNextOl($fy);

                // Tentukan Budget yang sedang Aktif Digunakan (Berdasarkan Bulan Berjalan)
                $activeBudgetOl = null;
                $currentMonth = $today->month;
                if ($currentMonth >= 5 && $currentMonth <= 7) $activeBudgetOl = 'OL1';
                elseif ($currentMonth >= 8 && $currentMonth <= 10) $activeBudgetOl = 'OL2';
                elseif ($currentMonth == 11 || $currentMonth == 12 || $currentMonth <= 4) $activeBudgetOl = 'OL3';
            @endphp
            <div class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-700 flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg> Jadwal Proses Budgeting (Outlook) — FY {{ now()->year }}</p>
                    <div class="flex gap-2">
                        @if($activeBudgetOl)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold" title="Budget yang sedang digunakan untuk transaksi saat ini">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Budget Berjalan: {{ $activeBudgetOl }}
                        </span>
                        @endif
                        @if($nextUploadOl)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold animate-pulse" title="Jadwal pengisian budget yang sedang dibuka saat ini">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Masa Upload: {{ $nextUploadOl }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto pb-2">
                    <table class="w-full text-xs text-slate-600 whitespace-nowrap min-w-[500px]">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Outlook</th>
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Periode Budget</th>
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Batas Upload</th>
                                <th class="pb-1.5 text-center font-semibold text-slate-500">Status Upload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($olSchedules as $ols)
                            @php
                                $dueDate = \Carbon\Carbon::parse($ols['due'])->endOfDay();
                                $isPast = $today->gt($dueDate);
                                $isNextUpload = $nextUploadOl === $ols['ol'];
                                $isCurrentlyRunning = $activeBudgetOl === $ols['ol'];
                                $daysLeft = $isPast ? 0 : (int) ceil($today->floatDiffInDays($dueDate));
                            @endphp
                            <tr class="border-b border-slate-100 {{ $isNextUpload ? 'bg-indigo-50/60' : '' }}">
                                <td class="py-1.5">
                                    <span class="font-bold {{ $ols['type'] === 'adjustment' ? 'text-orange-600' : 'text-indigo-600' }}">
                                        {{ $ols['ol'] }}
                                    </span>
                                    @if($isCurrentlyRunning)
                                    <span class="ml-1 text-[9px] px-1 py-0.5 bg-blue-100 text-blue-700 rounded-md font-semibold">Running</span>
                                    @endif
                                </td>
                                <td class="py-1.5">{{ $ols['periode'] }}</td>
                                <td class="py-1.5 font-medium {{ $isNextUpload ? 'text-indigo-700' : '' }}">{{ $ols['due_label'] }}</td>
                                <td class="py-1.5 text-center">
                                    @if($isPast)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-200 text-slate-500 text-[10px] font-semibold">✓ Ditutup</span>
                                    @elseif($isNextUpload)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-bold"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $daysLeft }} hari lagi</span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 text-[10px] font-semibold">Mendatang</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Daftar Cost Center Referensi --}}
            @if(isset($costCenters) && $costCenters->count())
            <div class="mt-3 p-4 bg-white border border-slate-200 rounded-xl">
                <details>
                    <summary class="text-xs font-semibold text-slate-700 cursor-pointer hover:text-indigo-600 transition-colors flex items-center gap-1.5 select-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                        Referensi Kode Cost Center — {{ $dept->dept_name }} ({{ $costCenters->count() }} cost center)
                    </summary>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        @foreach($costCenters as $cc)
                        <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-slate-50 hover:bg-indigo-50 transition-colors group">
                            <code class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 group-hover:bg-indigo-200 transition-colors shrink-0">
                                {{ $cc->cost_center_code ?? 'N/A' }}
                            </code>
                            <span class="text-[11px] text-slate-600 truncate">{{ $cc->cost_center_name }}</span>
                            <span class="ml-auto text-[9px] font-semibold px-1.5 py-0.5 rounded-full shrink-0
                                {{ $cc->expense_type === 'FOH' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $cc->expense_type }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </details>
            </div>
            @endif

            <div class="mt-5 flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit" id="parseBtn"
                        class="flex-1 py-2.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-all shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Proses & Preview Excel
                </button>
            </div>
        </form>
    </div>

    {{-- ── RIWAYAT UPLOAD ─────────────────────────── --}}
    @if($uploads->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 animate-page-delay-2">
        <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <div class="w-5 h-5 rounded-md bg-slate-100 flex items-center justify-center">
                <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 013.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 013.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 01-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875zm5.845 17.03a.75.75 0 001.06 0l3-3a.75.75 0 10-1.06-1.06l-1.72 1.72V12a.75.75 0 00-1.5 0v4.19l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3z"/>
                </svg>
            </div>
            Riwayat Upload Budget (10 Terakhir)
        </h3>

        <div class="space-y-2">
            @foreach($uploads as $upload)
            <a href="{{ route('budget.upload.show', $upload) }}"
               class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-slate-700">{{ $upload->outlook_number }}</span>
                        <span class="category-chip chip-{{ strtolower($upload->category) }}">{{ $upload->category }}</span>
                        <span class="status-badge {{ $upload->status === 'confirmed' ? 'status-confirmed' : 'status-draft' }}">
                            {{ $upload->status === 'confirmed' ? 'Confirmed' : 'Draft' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5 truncate">
                        FY{{ $upload->fiscal_year }}
                        @if($upload->outlook_period) — {{ $upload->outlook_period }} @endif
                        — {{ count($upload->items ?? []) }} item
                        — Rp {{ number_format($upload->total_amount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-xs text-slate-400 shrink-0">{{ $upload->created_at->format('d M Y') }}</div>
                <svg class="w-4 h-4 text-slate-300 shrink-0 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    {{-- ── MONITORING SISA PAGU PER COST CENTER ─── --}}
    @if(isset($costCenters) && $costCenters->count())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-6 animate-page-delay-2">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Monitoring Sisa Pagu per Cost Center</h3>
                    <p class="text-[11px] text-slate-400">FY {{ now()->year }} — {{ $dept->dept_name }}</p>
                </div>
            </div>
            {{-- Ringkasan total dept --}}
            @php
                $totalPlan     = $costCenters->sum(fn($cc) => $cc->currentBudget?->total_plan ?? 0);
                $totalUsed     = $costCenters->sum(fn($cc) => $cc->currentBudget?->total_used ?? 0);
                $totalReserved = $costCenters->sum(fn($cc) => $cc->currentBudget?->total_reserved ?? 0);
                $totalSisa     = $totalPlan - $totalUsed - $totalReserved;
                $usedPct       = $totalPlan > 0 ? round(($totalUsed + $totalReserved) / $totalPlan * 100, 1) : 0;
            @endphp
            <div class="text-right hidden sm:block">
                <p class="text-xs text-slate-400">Total Pagu</p>
                <p class="text-base font-bold text-slate-800">Rp {{ number_format($totalPlan, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-500">Sisa: <span class="font-semibold text-emerald-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</span></p>
            </div>
        </div>

        {{-- Total Progress Bar --}}
        @if($totalPlan > 0)
        <div class="mb-5 p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="flex justify-between text-[11px] text-slate-500 mb-1.5">
                <span>Utilisasi Keseluruhan</span>
                <span class="font-semibold {{ $usedPct >= 90 ? 'text-rose-600' : ($usedPct >= 70 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $usedPct }}% terpakai</span>
            </div>
            <div class="h-2 bg-slate-200 rounded-full overflow-hidden flex">
                <div class="h-full bg-rose-500 transition-all" style="width: {{ $totalPlan > 0 ? round($totalUsed / $totalPlan * 100, 1) : 0 }}%"></div>
                <div class="h-full bg-amber-400 transition-all" style="width: {{ $totalPlan > 0 ? round($totalReserved / $totalPlan * 100, 1) : 0 }}%"></div>
                <div class="h-full bg-emerald-400 transition-all" style="width: {{ $totalPlan > 0 ? max(0, 100 - round($totalUsed / $totalPlan * 100, 1) - round($totalReserved / $totalPlan * 100, 1)) : 0 }}%"></div>
            </div>
            <div class="flex items-center gap-4 mt-1.5 text-[10px] text-slate-400">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>Terpakai: Rp {{ number_format($totalUsed, 0, ',', '.') }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>Di-Hold: Rp {{ number_format($totalReserved, 0, ',', '.') }}</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>Sisa: Rp {{ number_format($totalSisa, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        {{-- Grouping per Plant --}}
        @php $groupedCC = $costCenters->groupBy('plant'); @endphp
        @foreach($groupedCC as $plant => $ccs)
        <div class="mb-4">
            <h4 class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 flex items-center gap-1.5">
                <span class="w-3 h-px bg-slate-300 inline-block"></span>
                Plant {{ $plant }}
                <span class="w-full h-px bg-slate-100 inline-block"></span>
            </h4>
            <div class="space-y-2">
                @foreach($ccs as $cc)
                @php
                    $plan     = (float) ($cc->currentBudget?->total_plan ?? 0);
                    $used     = (float) ($cc->currentBudget?->total_used ?? 0);
                    $reserved = (float) ($cc->currentBudget?->total_reserved ?? 0);
                    $sisa     = $plan - $used - $reserved;
                    $usedPct  = $plan > 0 ? round(($used + $reserved) / $plan * 100, 1) : 0;
                    $statusColor = $plan === 0 ? 'slate' : ($usedPct >= 90 ? 'rose' : ($usedPct >= 70 ? 'amber' : 'emerald'));
                    $statusLabel = $plan === 0 ? 'Belum Diset' : ($usedPct >= 90 ? 'Kritis' : ($usedPct >= 70 ? 'Perhatian' : 'Aman'));
                @endphp
                <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:shadow-sm transition-all bg-white">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <code class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 shrink-0">
                                {{ $cc->cost_center_code ?? 'N/A' }}
                            </code>
                            <span class="text-xs text-slate-600 truncate">{{ $cc->cost_center_name }}</span>
                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full shrink-0
                                {{ $cc->expense_type === 'FOH' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $cc->expense_type }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full
                                bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                    @if($plan > 0)
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden flex mb-2">
                        <div class="h-full bg-rose-400 transition-all" style="width: {{ $plan > 0 ? round($used / $plan * 100, 1) : 0 }}%"></div>
                        <div class="h-full bg-amber-300 transition-all" style="width: {{ $plan > 0 ? round($reserved / $plan * 100, 1) : 0 }}%"></div>
                        <div class="h-full bg-emerald-400 transition-all" style="width: {{ $plan > 0 ? max(0, 100 - round($used / $plan * 100, 1) - round($reserved / $plan * 100, 1)) : 0 }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-[10px] text-slate-400">Pagu</p>
                            <p class="text-[11px] font-semibold text-slate-700">Rp {{ number_format($plan, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400">Terpakai + Hold</p>
                            <p class="text-[11px] font-semibold text-rose-600">Rp {{ number_format($used + $reserved, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400">Sisa</p>
                            <p class="text-[11px] font-bold {{ $sisa < 0 ? 'text-rose-700' : 'text-emerald-700' }}">Rp {{ number_format($sisa, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-[10px] text-slate-400 italic">Pagu belum ditetapkan untuk tahun ini. Upload budget untuk mengisi pagu.</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
// ── Drag & Drop ────────────────────────────
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('excel_file');

dropZone.addEventListener('dragover', e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length) {
        fileInput.files = files;
        onFileSelected(fileInput);
    }
});

function onFileSelected(input) {
    const file = input.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    if (!['xlsx', 'xls'].includes(ext)) {
        alert('Hanya file .xlsx atau .xls yang diperbolehkan.');
        input.value = '';
        return;
    }

    document.getElementById('dropIcon').classList.add('hidden');
    const fi = document.getElementById('fileInfo');
    fi.classList.remove('hidden');
    fi.classList.add('flex');
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatBytes(file.size);
    document.getElementById('parseBtn').disabled = false;
}

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// ── Loading state on submit ────────────────
document.getElementById('uploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('parseBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Memproses Excel...
    `;
});
</script>
@endpush
