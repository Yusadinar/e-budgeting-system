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
        min-width: 100px;
        font-family: ui-monospace, monospace;
    }
    .ol-display {
        font-size: 56px;
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
    .add-row-btn {
        transition: all .15s;
    }
    .add-row-btn:hover { background: #f1f5f9; }
    .del-row-btn {
        opacity: 0;
        transition: opacity .15s;
    }
    tr:hover .del-row-btn { opacity: 1; }
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
    .chip-value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .chip-label {
        font-size: 10px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-top: 2px;
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .row-new { animation: rowIn .2s ease both; }
</style>
@endpush

@section('content')

<div class="max-w-full space-y-5 animate-page">

    {{-- ── HEADER INFO ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- OL Card --}}
        <div class="ol-bg rounded-2xl p-5 text-center shadow-md shadow-amber-300/40 flex flex-col items-center justify-center gap-1">
            <p class="section-label text-amber-700 mb-1">Outlook</p>
            <p class="ol-display">{{ $parsed['outlook_number'] }}</p>
            @if($parsed['outlook_period'])
            <p class="text-xs text-amber-700 font-medium mt-1">{{ $parsed['outlook_period'] }}</p>
            @endif
        </div>

        {{-- Info Card --}}
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
                    <p class="section-label text-slate-400">File</p>
                    <p class="text-xs font-medium text-slate-500 mt-0.5 truncate">{{ $parsed['file_name'] ?? '—' }}</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z"/></svg>
                    <span><strong>Periksa data berikut sebelum menyimpan.</strong> Anda dapat mengedit langsung di tabel (klik sel untuk edit).</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── FORM REVIEW + TABLE ───────────────────────────── --}}
    <form method="POST" action="{{ route('budget.upload.store') }}" id="reviewForm">
        @csrf

        {{-- Hidden fields --}}
        <input type="hidden" name="outlook_number" id="hOutlook"  value="{{ $parsed['outlook_number'] }}">
        <input type="hidden" name="outlook_period" id="hPeriod"   value="{{ $parsed['outlook_period'] ?? '' }}">
        <input type="hidden" name="category"       id="hCategory" value="{{ $parsed['category'] }}">
        <input type="hidden" name="fiscal_year"    id="hFiscal"   value="{{ $parsed['fiscal_year'] }}">
        <input type="hidden" name="file_name"      value="{{ $parsed['file_name'] ?? '' }}">

        {{-- ── Header metadata (editable) ─────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-card p-5">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-4">Informasi Outlook</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                {{-- Outlook Number --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Outlook</label>
                    <select name="outlook_number" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        @foreach(['OL1','OL2','OL3','OL2ADJ'] as $ol)
                        <option value="{{ $ol }}" {{ $parsed['outlook_number'] === $ol ? 'selected' : '' }}>{{ $ol }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Periode Outlook</label>
                    <input type="text" name="outlook_period" value="{{ $parsed['outlook_period'] ?? '' }}"
                           placeholder="Cth: Mei 2026 – Jul 2026"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        <option value="CAPEX" {{ $parsed['category'] === 'CAPEX' ? 'selected' : '' }}>CAPEX</option>
                        <option value="FOH"   {{ $parsed['category'] === 'FOH'   ? 'selected' : '' }}>FOH</option>
                        <option value="OPEX"  {{ $parsed['category'] === 'OPEX'  ? 'selected' : '' }}>OPEX</option>
                    </select>
                </div>

                {{-- Tahun --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tahun Fiskal</label>
                    <select name="fiscal_year" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        @for($y = now()->year - 1; $y <= now()->year + 3; $y++)
                        <option value="{{ $y }}" {{ $parsed['fiscal_year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mt-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                <textarea name="notes" rows="2"
                          placeholder="Catatan tambahan untuk budget ini..."
                          class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-800 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-none transition-all"></textarea>
            </div>
        </div>

        {{-- ── Item Table ────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-card overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Item Budget</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Klik pada sel untuk mengedit nilai. Tambah baris jika diperlukan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="summary-chip">
                        <span class="chip-value" id="totalItems">{{ count($parsed['items']) }}</span>
                        <span class="chip-label">Items</span>
                    </div>
                    <div class="summary-chip">
                        <span class="chip-value text-indigo-600" id="totalAmount">Rp {{ number_format($parsed['total_amount'], 0, ',', '.') }}</span>
                        <span class="chip-label">Total 2026</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="review-table w-full border-collapse" id="itemTable">
                    <thead>
                        <tr>
                            <th class="text-center w-10">No</th>
                            <th class="min-w-48">Description</th>
                            <th>Cost Center</th>
                            <th>AJU / IA</th>
                            <th>Preventive</th>
                            <th class="text-right">2025 (Rp)</th>
                            <th class="text-right bg-indigo-50 text-indigo-600">2026 (Rp)</th>
                            <th class="text-right">2027 (Rp)</th>
                            <th class="text-right">Actual Jan</th>
                            <th class="text-right">Actual Feb</th>
                            <th class="text-right">Actual Mar</th>
                            <th class="text-right">Actual Apr</th>
                            <th class="text-right">Actual Mei</th>
                            <th class="text-right">Total Actual</th>
                            <th class="text-right">Saldo</th>
                            <th class="w-8"></th>
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
                                       class="cell-input" style="min-width:180px"
                                       placeholder="Nama item..." required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][cost_center]"
                                       value="{{ $item['cost_center'] ?? '' }}"
                                       class="cell-input" placeholder="M-201-...">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][aju_ia]"
                                       value="{{ $item['aju_ia'] ?? '' }}"
                                       class="cell-input" placeholder="Office Exp...">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][preventive]"
                                       value="{{ $item['preventive'] ?? '' }}"
                                       class="cell-input">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][amount_2025]"
                                       value="{{ $item['amount_2025'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1"
                                       oninput="recalcTotal()">
                            </td>
                            <td class="bg-indigo-50/50">
                                <input type="number" name="items[{{ $i }}][amount_2026]"
                                       value="{{ $item['amount_2026'] ?? 0 }}"
                                       class="cell-input amount !border-indigo-200 font-semibold text-indigo-700" min="0" step="1"
                                       oninput="recalcTotal()">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][amount_2027]"
                                       value="{{ $item['amount_2027'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_jan]"
                                       value="{{ $item['actual_jan'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_feb]"
                                       value="{{ $item['actual_feb'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_mar]"
                                       value="{{ $item['actual_mar'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_apr]"
                                       value="{{ $item['actual_apr'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_mei]"
                                       value="{{ $item['actual_mei'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][actual_total]"
                                       value="{{ $item['actual_total'] ?? 0 }}"
                                       class="cell-input amount" min="0" step="1">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][saldo]"
                                       value="{{ $item['saldo'] ?? 0 }}"
                                       class="cell-input amount" step="1">
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
                            <td colspan="16" class="p-2">
                                <button type="button" onclick="addRow()"
                                        class="add-row-btn w-full py-2 rounded-lg border border-dashed border-slate-300 text-xs text-slate-500 hover:text-indigo-600 hover:border-indigo-300 flex items-center justify-center gap-1.5 transition-all">
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

// ── Recalculate totals ─────────────────────
function recalcTotal() {
    const inputs = document.querySelectorAll('input[name$="[amount_2026]"]');
    let total = 0;
    inputs.forEach(inp => total += parseFloat(inp.value || 0));
    document.getElementById('totalAmount').textContent = 'Rp ' + total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    document.getElementById('totalItems').textContent = document.querySelectorAll('#itemBody tr').length;
}

// ── Add new row ────────────────────────────
function addRow() {
    const idx  = rowCount++;
    const tbody = document.getElementById('itemBody');
    const rowNum = tbody.querySelectorAll('tr').length + 1;

    const tr = document.createElement('tr');
    tr.className = 'row-new';
    tr.setAttribute('data-row', idx);
    tr.innerHTML = `
        <td class="text-center text-xs text-slate-400">
            <input type="hidden" name="items[${idx}][no]" value="${rowNum}">
            ${rowNum}
        </td>
        <td><input type="text"   name="items[${idx}][description]"  class="cell-input" style="min-width:180px" placeholder="Nama item..." required></td>
        <td><input type="text"   name="items[${idx}][cost_center]"  class="cell-input" placeholder="M-201-..."></td>
        <td><input type="text"   name="items[${idx}][aju_ia]"       class="cell-input" placeholder="Office Exp..."></td>
        <td><input type="text"   name="items[${idx}][preventive]"   class="cell-input"></td>
        <td><input type="number" name="items[${idx}][amount_2025]"  class="cell-input amount" value="0" min="0" step="1" oninput="recalcTotal()"></td>
        <td class="bg-indigo-50/50">
            <input type="number" name="items[${idx}][amount_2026]"  class="cell-input amount !border-indigo-200 font-semibold text-indigo-700" value="0" min="0" step="1" oninput="recalcTotal()">
        </td>
        <td><input type="number" name="items[${idx}][amount_2027]"  class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_jan]"   class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_feb]"   class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_mar]"   class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_apr]"   class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_mei]"   class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][actual_total]" class="cell-input amount" value="0" min="0" step="1"></td>
        <td><input type="number" name="items[${idx}][saldo]"        class="cell-input amount" value="0" step="1"></td>
        <td class="text-center">
            <button type="button" class="del-row-btn w-6 h-6 rounded-md hover:bg-rose-50 flex items-center justify-center" onclick="deleteRow(this)" title="Hapus baris">
                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    recalcTotal();
    tr.querySelector('input[name$="[description]"]').focus();
}

// ── Delete row ─────────────────────────────
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

// ── Submit loading ─────────────────────────
document.getElementById('reviewForm').addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        Menyimpan...
    `;
});

// Initial calc
recalcTotal();
</script>
@endpush
