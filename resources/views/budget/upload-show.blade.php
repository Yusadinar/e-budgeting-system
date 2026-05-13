@extends('layouts.app')
@section('title', 'Detail Upload Budget')
@section('page-title', 'Detail Upload Budget')
@section('page-subtitle', 'Lampiran Budget — ' . $budgetUpload->outlook_number . ' — ' . $budgetUpload->category)

@push('styles')
<style>
    .detail-table th {
        background: #f1f5f9;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        padding: 8px 10px;
        white-space: nowrap;
    }
    .detail-table td {
        padding: 7px 10px;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .detail-table tr:last-child td { border-bottom: none; }
    .detail-table tr:hover td { background: #f8fafc; }
    .detail-table .amount { text-align: right; font-family: ui-monospace, monospace; }
    .ol-badge {
        font-size: 48px;
        font-weight: 900;
        color: #1e293b;
        line-height: 1;
    }
    .ol-bg { background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%); }
    .category-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }
    .pill-capex { background: #fef3c7; color: #92400e; }
    .pill-foh   { background: #dcfce7; color: #166534; }
    .pill-opex  { background: #dbeafe; color: #1e40af; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px; }
    .info-item label { font-size: 10px; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; font-weight: 600; }
    .info-item p { font-size: 13px; font-weight: 600; color: #1e293b; margin-top: 3px; }
</style>
@endpush

@section('content')

<div class="max-w-full space-y-5 animate-page">

    {{-- ── HEADER ─────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">

        {{-- OL --}}
        <div class="ol-bg rounded-2xl p-5 text-center shadow-md shadow-amber-300/40 flex flex-col items-center justify-center">
            <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Outlook</p>
            <p class="ol-badge">{{ $budgetUpload->outlook_number }}</p>
            @if($budgetUpload->outlook_period)
            <p class="text-xs text-amber-700 font-medium mt-1">{{ $budgetUpload->outlook_period }}</p>
            @endif
        </div>

        {{-- Info --}}
        <div class="sm:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-card p-5 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-800">Lampiran Budget</h2>
                    <span class="category-pill pill-{{ strtolower($budgetUpload->category) }}">{{ $budgetUpload->category }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $budgetUpload->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $budgetUpload->status === 'confirmed' ? '✓ Confirmed' : 'Draft' }}
                    </span>
                </div>
                <a href="{{ route('budget.upload.index') }}"
                   class="text-xs text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Departemen</label>
                    <p>{{ $budgetUpload->department->dept_name }}</p>
                </div>
                <div class="info-item">
                    <label>Tahun Fiskal</label>
                    <p>FY {{ $budgetUpload->fiscal_year }}</p>
                </div>
                <div class="info-item">
                    <label>Total Item</label>
                    <p>{{ count($budgetUpload->items ?? []) }} item</p>
                </div>
                <div class="info-item">
                    <label>Total Amount 2026</label>
                    <p class="text-indigo-600">Rp {{ number_format($budgetUpload->total_amount, 0, ',', '.') }}</p>
                </div>
                <div class="info-item">
                    <label>Diupload oleh</label>
                    <p>{{ $budgetUpload->uploader->name }}</p>
                </div>
                <div class="info-item">
                    <label>Tanggal Upload</label>
                    <p>{{ $budgetUpload->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            @if($budgetUpload->file_name)
            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <span class="text-xs text-slate-600 font-medium truncate">{{ $budgetUpload->file_name }}</span>
            </div>
            @endif

            @if($budgetUpload->notes)
            <div class="px-3 py-2 bg-indigo-50 rounded-lg border border-indigo-100">
                <p class="text-xs text-slate-500 font-semibold mb-0.5">Catatan:</p>
                <p class="text-xs text-slate-700">{{ $budgetUpload->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── ITEM TABLE ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-card overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800">Daftar Item Budget</h3>
            <p class="text-xs text-slate-400 mt-0.5">Total {{ count($budgetUpload->items ?? []) }} item dari file <em>{{ $budgetUpload->file_name ?? 'Excel' }}</em></p>
        </div>

        <div class="overflow-x-auto">
            <table class="detail-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="min-w-48">Description</th>
                        <th>Cost Center</th>
                        <th>AJU / IA</th>
                        <th>Preventive</th>
                        <th class="amount">2025 (Rp)</th>
                        <th class="amount bg-indigo-50 text-indigo-600">2026 (Rp)</th>
                        <th class="amount">2027 (Rp)</th>
                        <th class="amount">Actual Jan</th>
                        <th class="amount">Actual Feb</th>
                        <th class="amount">Actual Mar</th>
                        <th class="amount">Actual Apr</th>
                        <th class="amount">Actual Mei</th>
                        <th class="amount">Total Actual</th>
                        <th class="amount">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgetUpload->items ?? [] as $i => $item)
                    <tr>
                        <td class="text-center text-slate-400">{{ $i + 1 }}</td>
                        <td class="font-medium text-slate-800">{{ $item['description'] ?? '—' }}</td>
                        <td>{{ $item['cost_center'] ?? '—' }}</td>
                        <td>{{ $item['aju_ia'] ?? '—' }}</td>
                        <td>{{ $item['preventive'] ?? '—' }}</td>
                        <td class="amount">{{ number_format($item['amount_2025'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount bg-indigo-50/50 font-semibold text-indigo-700">{{ number_format($item['amount_2026'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['amount_2027'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_jan'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_feb'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_mar'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_apr'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_mei'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount">{{ number_format($item['actual_total'] ?? 0, 0, ',', '.') }}</td>
                        <td class="amount {{ ($item['saldo'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ number_format($item['saldo'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="text-center py-8 text-sm text-slate-400">Tidak ada item ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($budgetUpload->items ?? []) > 0)
                <tfoot>
                    <tr class="bg-slate-50">
                        <td colspan="5" class="px-3 py-2 text-xs font-bold text-slate-500 uppercase">TOTAL</td>
                        <td class="amount px-3 py-2 font-bold text-slate-700">
                            {{ number_format(collect($budgetUpload->items)->sum('amount_2025'), 0, ',', '.') }}
                        </td>
                        <td class="amount px-3 py-2 font-bold text-indigo-700 bg-indigo-50">
                            {{ number_format(collect($budgetUpload->items)->sum('amount_2026'), 0, ',', '.') }}
                        </td>
                        <td class="amount px-3 py-2 font-bold text-slate-700">
                            {{ number_format(collect($budgetUpload->items)->sum('amount_2027'), 0, ',', '.') }}
                        </td>
                        <td colspan="5" class="px-3 py-2"></td>
                        <td class="amount px-3 py-2 font-bold text-slate-700">
                            {{ number_format(collect($budgetUpload->items)->sum('actual_total'), 0, ',', '.') }}
                        </td>
                        <td class="amount px-3 py-2 font-bold {{ collect($budgetUpload->items)->sum('saldo') >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ number_format(collect($budgetUpload->items)->sum('saldo'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

@endsection
