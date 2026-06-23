@extends('layouts.app')
@section('title', 'Review Budget Excel')
@section('page-title', 'Review Budget Excel')
@section('page-subtitle', 'Periksa & sesuaikan data sebelum disimpan')

@push('styles')
<style>
    .review-table th {
        background: #f1f5f9;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        padding: 8px 10px;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .review-table td {
        padding: 6px 8px;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .review-table tr:last-child td { border-bottom: none; }
    .review-table tr:hover td { background: #f8fafc; }
    .cell-input {
        width: 100%;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 3px 6px;
        font-size: 12px;
        color: #1e293b;
        transition: border-color .15s, background .15s;
        min-width: 80px;
    }
    .cell-input:focus {
        outline: none;
        border-color: #818cf8;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(129,140,248,.15);
    }
    .cell-input.amount {
        text-align: right;
        min-width: 95px;
        font-family: ui-monospace, monospace;
    }
    .ol-display {
        font-size: 48px;
        font-weight: 900;
        color: #1e293b;
        line-height: 1;
        letter-spacing: -.02em;
    }
    .ol-bg {
        background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%);
    }
    .section-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
    }
    .summary-chip {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        min-width: 100px;
    }
    .chip-value { font-size: 14px; font-weight: 700; color: #1e293b; }
    .chip-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }
    @keyframes rowIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .row-new { animation: rowIn .2s ease both; }
    .del-row-btn { opacity: 0; transition: opacity .15s; }
    tr:hover .del-row-btn { opacity: 1; }
</style>
@endpush

@section('content')

@php
    $budgetCols = $parsed['budget_columns'] ?? [];
    $colCount = count($budgetCols);
@endphp

<div class="max-w-full space-y-5 animate-page">

    {{-- ── HEADER INFO ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="ol-bg rounded-2xl p-5 text-center shadow-md shadow-amber-300/40 flex flex-col items-center justify-center gap-1">
            <p class="section-label text-amber-700 mb-1">Outlook</p>
            <p class="ol-display">{{ $parsed['outlook_number'] }}</p>
            <p class="text-xs text-amber-700 font-medium mt-1">FY {{ $parsed['fiscal_year'] }}</p>
        </div>

        <div class="sm:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-card p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800">Review Data Budget</h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $parsed['category'] === 'CAPEX' ? 'bg-amber-100 text-amber-700' :
                       ($parsed['category'] === 'FOH'  ? 'bg-emerald-100 text-emerald-700' :
                                                          'bg-blue-100 text-blue-700') }}">
                    {{ $parsed['category'] }}
                </span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div>
                    <p class="section-label text-slate-400">Departemen</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $dept->dept_name }}</p>
                </div>
                <div>
                    <p class="section-label text-slate-400">Tahun Fiskal</p>
                    <p class="text-sm font-semibold text-slate-700 mt-0.5">FY {{ $parsed['fiscal_year'] }}</p>
                </div>
                <div>
                    <p class="section-label text-slate-400">Kolom Budget</p>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">{{ $colCount }} kolom ({{ collect($budgetCols)->where('type', 'monthly')->count() }} monthly, {{ collect($budgetCols)->where('type', 'ytd')->count() }} YTD)</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"/></svg>
                    <span><strong>Periksa data berikut sebelum menyimpan.</strong> Klik pada sel untuk mengedit langsung.</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── FORM REVIEW + TABLE ───────────────────────────── --}}
    <form method="POST" action="{{ route('budget.upload.store') }}" id="reviewForm" class="space-y-6">
        @csrf

        <input type="hidden" name="outlook_number" value="{{ $parsed['outlook_number'] }}">
        <input type="hidden" name="outlook_period"  value="{{ $parsed['outlook_period'] ?? '' }}">
        <input type="hidden" name="category"        value="{{ $parsed['category'] }}">
        <input type="hidden" name="fiscal_year"     value="{{ $parsed['fiscal_year'] }}">
        <input type="hidden" name="file_name"       value="{{ $parsed['file_name'] ?? '' }}">
        <input type="hidden" name="budget_columns"  value="{{ json_encode($budgetCols) }}">

        {{-- ── Metadata ─────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-5">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Informasi Outlook</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Outlook</label>
                    <select name="outlook_number" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        @foreach(['OL1','OL2','OL3','OL2ADJ'] as $ol)
                        <option value="{{ $ol }}" {{ $parsed['outlook_number'] === $ol ? 'selected' : '' }}>{{ $ol }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Periode Outlook</label>
                    <input type="text" name="outlook_period" value="{{ $parsed['outlook_period'] ?? '' }}"
                           placeholder="Cth: Mei 2026 – Jul 2026"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        <option value="CAPEX" {{ $parsed['category'] === 'CAPEX' ? 'selected' : '' }}>CAPEX</option>
                        <option value="FOH"   {{ $parsed['category'] === 'FOH'   ? 'selected' : '' }}>FOH</option>
                        <option value="OPEX"  {{ $parsed['category'] === 'OPEX'  ? 'selected' : '' }}>OPEX</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tahun Fiskal</label>
                    <select name="fiscal_year" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        @for($y = now()->year - 1; $y <= now()->year + 3; $y++)
                        <option value="{{ $y }}" {{ $parsed['fiscal_year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                <textarea name="notes" rows="2" placeholder="Catatan tambahan..." class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-none transition-all"></textarea>
            </div>
        </div>

        {{-- ── Item Table ────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Item Budget</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Klik pada sel untuk mengedit nilai. Tambah baris jika diperlukan.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="summary-chip">
                        <span class="chip-value" id="totalItems">{{ count($parsed['items']) }}</span>
                        <span class="chip-label">Items</span>
                    </div>
                    <div class="summary-chip border-emerald-200 bg-emerald-50">
                        <span class="chip-value text-emerald-700" id="currentBudget" data-val="{{ $dept->remaining_budget }}">Rp {{ number_format($dept->remaining_budget, 0, ',', '.') }}</span>
                        <span class="chip-label text-emerald-600">Sisa Pagu</span>
                    </div>
                    <div class="summary-chip border-amber-200 bg-amber-50">
                        <span class="chip-value text-amber-700" id="totalAmount">Rp {{ number_format($parsed['total_amount'], 0, ',', '.') }}</span>
                        <span class="chip-label text-amber-600">Total Upload</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="review-table w-full border-collapse" id="itemTable">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center w-10">No</th>
                            <th rowspan="2" class="min-w-48">Description</th>
                            <th rowspan="2">Cost Center</th>
                            <th rowspan="2">AJU / IA</th>
                            <th rowspan="2">Preventive</th>
                            @php
                                $groupedCols = collect($budgetCols)->groupBy('year');
                            @endphp
                            @foreach($groupedCols as $year => $cols)
                                @php
                                    $hasMonthly = $cols->where('type', 'monthly')->count() > 0;
                                    $hasYtd = $cols->where('type', 'ytd')->count() > 0;
                                @endphp
                                @if($hasMonthly)
                                <th colspan="{{ $cols->where('type', 'monthly')->count() }}" class="text-center bg-indigo-50 text-indigo-600 border-b border-indigo-100">Budget {{ $year }}</th>
                                @endif
                                @if($hasYtd)
                                    @foreach($cols->where('type', 'ytd') as $ytdCol)
                                    <th rowspan="2" class="text-center bg-emerald-50 text-emerald-700 align-middle">{{ $year }} YTD</th>
                                    @endforeach
                                @endif
                            @endforeach
                            <th rowspan="2" class="text-right bg-amber-50 text-amber-700 align-middle">Total</th>
                            <th rowspan="2" class="w-8"></th>
                        </tr>
                        <tr>
                            @foreach($budgetCols as $col)
                                @if($col['type'] === 'monthly')
                                <th class="text-right">{{ $col['month'] }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        @foreach($parsed['items'] as $i => $item)
                        <tr data-row="{{ $i }}">
                            <td class="text-center text-xs text-slate-400">
                                <input type="hidden" name="items[{{ $i }}][no]" value="{{ $item['no'] ?? ($i + 1) }}">
                                {{ $i + 1 }}
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][description]"
                                       value="{{ $item['description'] ?? '' }}"
                                       class="cell-input" style="min-width:180px" placeholder="Nama item..." required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][cost_center]"
                                       value="{{ $item['cost_center'] ?? '' }}"
                                       class="cell-input" placeholder="P-902-...">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][aju_ia]"
                                       value="{{ $item['aju_ia'] ?? '' }}"
                                       class="cell-input" placeholder="AJU...">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][preventive]"
                                       value="{{ $item['preventive'] ?? '' }}"
                                       class="cell-input">
                            </td>
                            @foreach($budgetCols as $cIdx => $col)
                            <td class="{{ $col['type'] === 'ytd' ? 'bg-emerald-50/30' : '' }}">
                                <input type="text" name="items[{{ $i }}][amounts][{{ $cIdx }}]"
                                       value="{{ number_format($item['amounts'][$col['key']] ?? 0, 0, '', '.') }}"
                                       class="cell-input amount {{ $col['type'] === 'ytd' ? 'text-emerald-700' : '' }}"
                                       oninput="formatNum(this); recalcRow(this)">
                            </td>
                            @endforeach
                            <td class="bg-amber-50/50">
                                <input type="text" class="cell-input amount font-bold text-amber-700 bg-transparent" data-role="row-total"
                                       value="{{ number_format(array_sum($item['amounts'] ?? []), 0, '', '.') }}" readonly tabindex="-1">
                            </td>
                            <td class="text-center">
                                <button type="button" class="del-row-btn w-6 h-6 rounded-md hover:bg-rose-50 flex items-center justify-center" onclick="deleteRow(this)" title="Hapus baris">
                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="{{ 5 + $colCount + 2 }}" class="p-2">
                                <button type="button" onclick="addRow()"
                                        class="w-full py-2 rounded-lg border border-dashed border-slate-300 text-xs text-slate-500 hover:text-indigo-600 hover:border-indigo-300 flex items-center justify-center gap-1.5 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    Tambah Baris
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Action Buttons ──────────────────────────── --}}
        <div class="flex flex-col sm:flex-row gap-3 pb-6">
            <a href="{{ route('budget.upload.index') }}"
               class="flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Upload Ulang
            </a>
            <button type="submit" id="saveBtn"
                    class="flex-1 flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-all shadow-lg shadow-emerald-500/25">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Konfirmasi & Simpan Budget
            </button>
        </div>
    </form>

</div>

@endsection

@push('scripts')
<script>
let rowCount = @json(count($parsed['items']));
const budgetColCount = @json($colCount);

function formatNum(input) {
    let val = input.value.replace(/[^0-9\-]/g, '');
    if (val === '' || val === '-') { input.value = val; return; }
    input.value = parseInt(val, 10).toLocaleString('id-ID');
}

function recalcTotal() {
    let grandTotal = 0;
    document.querySelectorAll('#itemBody tr').forEach(tr => {
        const amtInputs = tr.querySelectorAll('input.amount:not([data-role="row-total"])');
        let rowTotal = 0;
        amtInputs.forEach(inp => {
            rowTotal += parseFloat(inp.value.replace(/\./g, '') || 0);
        });
        const totalInp = tr.querySelector('[data-role="row-total"]');
        if (totalInp) totalInp.value = rowTotal.toLocaleString('id-ID');
        grandTotal += rowTotal;
    });

    document.getElementById('totalAmount').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    document.getElementById('totalItems').textContent = document.querySelectorAll('#itemBody tr').length;
}

function recalcRow(el) { recalcTotal(); }

function addRow() {
    const idx = rowCount++;
    const tbody = document.getElementById('itemBody');
    const rowNum = tbody.querySelectorAll('tr').length + 1;

    let amountCells = '';
    for (let c = 0; c < budgetColCount; c++) {
        amountCells += `<td><input type="text" name="items[${idx}][amounts][${c}]" class="cell-input amount" value="0" oninput="formatNum(this); recalcRow(this)"></td>`;
    }

    const tr = document.createElement('tr');
    tr.className = 'row-new';
    tr.setAttribute('data-row', idx);
    tr.innerHTML = `
        <td class="text-center text-xs text-slate-400">
            <input type="hidden" name="items[${idx}][no]" value="${rowNum}">${rowNum}
        </td>
        <td><input type="text" name="items[${idx}][description]" class="cell-input" style="min-width:180px" placeholder="Nama item..." required></td>
        <td><input type="text" name="items[${idx}][cost_center]" class="cell-input" placeholder="P-902-..."></td>
        <td><input type="text" name="items[${idx}][aju_ia]" class="cell-input" placeholder="AJU..."></td>
        <td><input type="text" name="items[${idx}][preventive]" class="cell-input"></td>
        ${amountCells}
        <td class="bg-amber-50/50"><input type="text" class="cell-input amount font-bold text-amber-700" data-role="row-total" value="0" readonly tabindex="-1"></td>
        <td class="text-center">
            <button type="button" class="del-row-btn w-6 h-6 rounded-md hover:bg-rose-50 flex items-center justify-center" onclick="deleteRow(this)">
                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    recalcTotal();
    tr.querySelector('input[name$="[description]"]').focus();
}

function deleteRow(btn) {
    const tr = btn.closest('tr');
    if (document.querySelectorAll('#itemBody tr').length <= 1) {
        alert('Minimal harus ada 1 item budget.');
        return;
    }
    tr.style.opacity = '0';
    tr.style.transform = 'translateY(-4px)';
    tr.style.transition = 'all .15s';
    setTimeout(() => { tr.remove(); recalcTotal(); }, 150);
}

document.getElementById('reviewForm').addEventListener('submit', function() {
    this.querySelectorAll('input.amount').forEach(inp => {
        inp.value = inp.value.replace(/\./g, '');
    });
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Menyimpan...`;
});

recalcTotal();
</script>
@endpush
