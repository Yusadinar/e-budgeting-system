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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium">📊 Format: .xlsx / .xls</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium">📁 Max 5 MB</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-xs font-medium">🏢 {{ $dept->dept_name }}</span>
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

            {{-- Info Template --}}
            <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-xl flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div>
                    <p class="text-sm font-semibold text-amber-800 flex items-center gap-1.5 mb-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"/></svg>
                        Gunakan Template Resmi
                    </p>
                    <p class="text-xs text-amber-700">Pastikan menggunakan template <strong>Lampiran Budget</strong> dari Dept. Accounting yang memiliki kolom: NO, DESCRIPTION, COST CENTER, AJU/IA, PREVENTIVE, dan kolom nominal per tahun (2025, 2026, 2027) serta Actual. Anda hanya perlu menyesuaikan/mengisi data pada baris yang disediakan di excel.</p>
                </div>
                <a href="{{ asset('templates/Template_Lampiran_Budget.xlsx') }}" download
                   class="shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download Template
                </a>
            </div>

            {{-- Panduan OL --}}
            <div class="mt-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <p class="text-xs font-semibold text-slate-700 mb-2">📅 Jadwal Proses Budgeting (Outlook)</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-slate-600">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Outlook</th>
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Periode</th>
                                <th class="pb-1.5 text-left font-semibold text-slate-500">Due Date</th>
                            </tr>
                        </thead>
                        <tbody class="space-y-1">
                            <tr class="border-b border-slate-100">
                                <td class="py-1 font-bold text-indigo-600">OL1</td>
                                <td class="py-1">Mei 2026 – Jul 2026</td>
                                <td class="py-1">10 Mei 2026</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-1 font-bold text-indigo-600">OL2</td>
                                <td class="py-1">Agus 2026 – Okt 2026</td>
                                <td class="py-1">10 Agustus 2026</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="py-1 font-bold text-indigo-600">OL3</td>
                                <td class="py-1">Nov 2026 – Apr 2027</td>
                                <td class="py-1">10 November 2026</td>
                            </tr>
                            <tr>
                                <td class="py-1 font-bold text-orange-600">OL2 ADJ</td>
                                <td class="py-1">Sept 2026 – Des 2026</td>
                                <td class="py-1">10 Oktober 2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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
