@extends('layouts.app')
@section('title', 'Preview Proposal Harga — ' . $proposalHarga->ph_number)
@section('page-title', 'Preview Proposal Harga')
@section('page-subtitle', $proposalHarga->ph_number)

@push('styles')
<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
    }
    body { 
        background:#fff!important; 
        zoom: 95%; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact; 
    }
    #sidebar, #sidebar-overlay, header, footer, .no-print { display:none!important; }
    #main-content { margin:0!important; }
    main { padding:0!important; }
    .paper-wrapper { overflow: visible!important; }
    .paper { box-shadow:none!important; border:none!important; padding: 8mm!important; margin: 0!important; width: 100%!important; box-sizing: border-box!important; min-height: auto!important; display: block!important; }
    .paper::before { display: none; }
}
.paper-wrapper {
    overflow-x: auto;
    padding-bottom: 24px;
}
.paper {
    background: #fff; 
    width: 297mm;
    min-height: 210mm;
    margin: 0 auto;
    border: 1px solid #cbd5e1; 
    border-radius: 4px;
    box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 24px 32px; 
    position: relative;
    display: flex;
    flex-direction: column;
}
.paper::before { content:''; position:absolute; top:0; left:0; right:0; height:6px; background:linear-gradient(90deg,#4f46e5,#6366f1,#818cf8); border-radius:4px 4px 0 0; }
.btn-print { display:inline-flex; align-items:center; gap:6px; padding:8px 20px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:500; cursor:pointer; }
.btn-print:hover { background:#e2e8f0; }

/* Custom table borders for print */
.ph-table { border-collapse: collapse; width: 100%; text-align: center; font-size: 10px; }
.ph-table th, .ph-table td { border: 1px solid #000; padding: 4px 6px; }
.ph-table th { background-color: #f3f4f6; } /* gray-100 equivalent */
</style>
@endpush

@section('content')
<div class="max-w-[297mm] mx-auto w-full mb-4 relative flex items-center justify-between no-print animate-page">
    <button type="button" onclick="if(window.history.length <= 1) { window.close(); } else { history.back(); }" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors bg-white/50 px-3 py-1.5 rounded-lg border border-slate-200/50 hover:bg-white hover:shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline">Kembali</span>
        <span class="sm:hidden">Back</span>
    </button>
    
    <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 hidden sm:flex">
        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold tracking-widest uppercase shadow-sm border border-black/5" style="{{ $proposalHarga->status === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($proposalHarga->status === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $proposalHarga->status ?? 'Draft') }}
        </span>
    </div>

    <div class="flex items-center gap-3">
        <span class="sm:hidden inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm" style="{{ $proposalHarga->status === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($proposalHarga->status === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $proposalHarga->status ?? 'Draft') }}
        </span>
        <button onclick="window.print()" class="justify-center inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span class="hidden sm:inline">Cetak Dokumen</span>
            <span class="sm:hidden">Cetak</span>
        </button>
    </div>
</div>

<div class="sm:hidden text-center text-[11px] text-slate-500 mb-2 no-print animate-page-delay-1 flex items-center justify-center gap-1.5 bg-indigo-50 py-1.5 rounded-lg max-w-[297mm] mx-auto">
    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
    <span>Geser area dokumen untuk melihat seluruh isi form</span>
</div>

<div class="paper-wrapper">
    <div class="paper animate-page text-black text-[11px]" style="font-family: Arial, Helvetica, sans-serif;">
        <!-- WRAPPER KOTAK OUTLINE -->
        <div class="border-[1.5px] border-black p-5 flex-1 flex flex-col relative">
        
        <div class="absolute top-1.5 right-2 text-[8px] font-bold">FISM-PUR-02-08-01</div>

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-2">
            <!-- Header Kiri -->
            <div class="w-1/3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/ippi-logo.png') }}" alt="Logo" class="h-10 object-contain" />
                    <div>
                        <h1 class="font-bold text-[12px] leading-tight">INTI PANTJA PRESS INDUSTRI</h1>
                        <p class="text-[9px] leading-tight">Procurement & Import Department / Purchasing Section</p>
                    </div>
                </div>
            </div>

            <!-- Header Tengah -->
            <div class="w-1/3 text-center">
                <h2 class="font-bold text-lg uppercase underline decoration-2 underline-offset-4 mb-1">Proposal Harga</h2>
                <p class="font-bold text-sm">No. {{ $proposalHarga->ph_number }}</p>
            </div>            <!-- Header Kanan -->
            <div class="w-1/3 flex flex-col items-end pt-1">
                <table class="ph-table w-48 text-[7px] border-black">
                    <tr><th class="w-16 text-left px-1 bg-gray-100 py-0">TYPE</th><td class="text-left px-1 py-0">{{ strtoupper($proposalHarga->type ?? '-') }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">DIV/DEPT</th><td class="text-left px-1 py-0">{{ strtoupper($proposalHarga->department_name ?? '-') }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">SEKSI</th><td class="text-left px-1 py-0">{{ strtoupper($proposalHarga->section_name ?? '-') }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">TGL REG</th><td class="text-left px-1 py-0">{{ $proposalHarga->created_at->format('d-M-y') }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">COST CTR</th><td class="text-left px-1 py-0">{{ strtoupper(explode(' ', $proposalHarga->cost_center ?? '-')[0]) }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">IO/ASSET</th><td class="text-left px-1 py-0">{{ strtoupper($proposalHarga->no_io_asset ?? '-') }}</td></tr>
                    <tr><th class="text-left px-1 bg-gray-100 py-0">BUDGET</th><td class="text-left px-1 font-bold py-0">{{ $proposalHarga->nominal_request ? 'BUDGETED' : 'UNBUDGETED' }}</td></tr>
                </table>
            </div>
        </div>

        <!-- SUBJECT -->
        <div class="mb-1 text-left text-[11px]">
            <span class="font-bold">SUBJECT:</span>
            <span class="uppercase">{{ $proposalHarga->subject }}</span>
        </div>

        @php
            $itemsData = is_string($proposalHarga->items_data) ? json_decode($proposalHarga->items_data, true) : $proposalHarga->items_data;
            
            // Detect if new format
            $isNewFormat = isset($itemsData['vendors']) && isset($itemsData['items']);
            
            if ($isNewFormat) {
                $vendors = $itemsData['vendors'];
                $itemsList = $itemsData['items'];
                $totalQty = $itemsData['summary_qty'] ?? 0;
            } else {
                // Fallback to old format
                $itemsList = is_array($itemsData) ? $itemsData : [];
                $vendors = [
                    [
                        'name' => $proposalHarga->selected_vendor_name ?? '-',
                        'delivery' => $proposalHarga->delivery_time ?? '-',
                        'quality' => $proposalHarga->quality ?? '-',
                        'payment' => $proposalHarga->payment_terms ?? '-',
                        'experience' => $proposalHarga->experience_non_ippi ?? '-'
                    ]
                ];
                $totalQty = array_sum(array_column($itemsList, 'qty'));
                foreach ($itemsList as &$itm) {
                    $itm['vendor_prices'] = [$itm['unit_price'] ?? 0];
                    $itm['winner_index'] = 0; // Legacy format always wins at index 0
                }
            }
        @endphp

        <!-- MAIN TABLE -->
        <table class="ph-table mb-2 table-fixed w-full break-inside-avoid">
            <thead>
                <tr>
                    <th rowspan="2" class="w-6">No</th>
                    <th rowspan="2">DESCRIPTION</th>
                    <th rowspan="2" class="w-10">QTY</th>
                    <th rowspan="2" class="w-10">UOM</th>
                    <th colspan="3" class="border-b-black">LAST ORDER</th>
                    @foreach($vendors as $vIndex => $vendor)
                        <th colspan="2" class="border-b-black bg-gray-50">
                            {{ strtoupper($vendor['name'] ?? 'VENDOR') }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <th class="w-16">PRICE PER</th>
                    <th class="w-16">VENDOR</th>
                    <th class="w-10">YEAR</th>
                    @foreach($vendors as $vIndex => $vendor)
                        <th class="w-20 bg-gray-50">UNIT PRICE</th>
                        <th class="w-20 bg-gray-50">TOTAL</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(count($itemsList) > 0)
                    @foreach($itemsList as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left px-2 break-words">{{ $item['description'] ?? '-' }}</td>
                        <td>{{ $item['qty'] ?? 0 }}</td>
                        <td>{{ strtoupper($item['uom'] ?? '-') }}</td>
                        <td class="text-right px-2">{{ number_format($item['last_price'] ?? 0, 0, ',', '.') }}</td>
                        <td class="break-words px-1">{{ $item['last_vendor'] ?? '-' }}</td>
                        <td>{{ $item['last_year'] ?? '-' }}</td>
                        
                        @foreach($vendors as $vIndex => $vendor)
                            @php
                                $uPrice = $item['vendor_prices'][$vIndex] ?? 0;
                                $qty = $item['qty'] ?? 0;
                                $total = $uPrice * $qty;
                                $isWin = isset($item['winner_index']) ? ($item['winner_index'] == $vIndex) : (0 == $vIndex);
                            @endphp
                            <td class="text-right px-2 {{ $isWin ? 'bg-yellow-100' : '' }}">{{ number_format($uPrice, 0, ',', '.') }}</td>
                            <td class="text-right px-2 {{ $isWin ? 'bg-yellow-100 font-bold text-red-600' : '' }}">{{ number_format($total, 0, ',', '.') }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ 7 + (count($vendors) * 2) }}" class="py-4">No item data available.</td>
                    </tr>
                @endif
                <!-- Empty rows for spacing -->
                @for($i=0; $i<1; $i++)
                <tr>
                    <td class="h-4"></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    @foreach($vendors as $vendor) <td></td><td></td> @endforeach
                </tr>
                @endfor
            </tbody>
            <tfoot>
                @php
                    $grandTotalWin = 0;
                    foreach($vendors as $vIndex => $vendor) {
                        $grandTotalWin += array_sum(array_map(function($itm) use ($vIndex) {
                            $isWin = isset($itm['winner_index']) ? ($itm['winner_index'] == $vIndex) : (0 == $vIndex);
                            return $isWin ? (($itm['qty'] ?? 0) * ($itm['vendor_prices'][$vIndex] ?? 0)) : 0;
                        }, $itemsList));
                    }
                @endphp
                <!-- Summary PO -->
                <tr class="font-bold bg-yellow-50 border-y-2 border-black text-[10px]">
                    <td colspan="2" class="text-right px-2">SUMMARY PURCHASE ORDER</td>
                    <td>{{ $totalQty }}</td>
                    <td colspan="4" class="text-right px-2">
                        TOTAL ESTIMASI:<br>
                        <span class="text-red-600">{{ number_format($grandTotalWin, 0, ',', '.') }}</span>
                    </td>
                    @foreach($vendors as $vIndex => $vendor)
                        @php
                            $vWinTotal = array_sum(array_map(function($itm) use ($vIndex) {
                                $isWin = isset($itm['winner_index']) ? ($itm['winner_index'] == $vIndex) : (0 == $vIndex);
                                return $isWin ? (($itm['qty'] ?? 0) * ($itm['vendor_prices'][$vIndex] ?? 0)) : 0;
                            }, $itemsList));
                        @endphp
                        <td colspan="2" class="text-right px-2 text-red-600">
                            TOTAL DIMENANGKAN<br>
                            {{ number_format($vWinTotal, 0, ',', '.') }}
                        </td>
                    @endforeach
                </tr>
                
                @php
                    $vendorWins = [];
                    foreach($vendors as $vIndex => $vendor) {
                        $vendorWins[$vIndex] = false;
                        foreach($itemsList as $itm) {
                            $winIdx = isset($itm['winner_index']) ? $itm['winner_index'] : 0;
                            if ($winIdx == $vIndex) {
                                $vendorWins[$vIndex] = true;
                                break;
                            }
                        }
                    }
                @endphp
                <!-- Terms -->
                <tr class="text-left bg-white font-bold text-[9px]">
                    <td colspan="7" class="text-right px-2 py-1 border-r border-black">DELIVERY</td>
                    @foreach($vendors as $vIndex => $vendor)
                        <td colspan="2" class="px-2 border-r border-black {{ $vendorWins[$vIndex] ? 'bg-yellow-50' : '' }}">{{ strtoupper($vendor['delivery'] ?? '-') }}</td>
                    @endforeach
                </tr>
                <tr class="text-left bg-white font-bold text-[9px]">
                    <td colspan="7" class="text-right px-2 py-1 border-r border-black border-t border-gray-300">QUALITY</td>
                    @foreach($vendors as $vIndex => $vendor)
                        <td colspan="2" class="px-2 border-r border-black border-t border-gray-300 {{ $vendorWins[$vIndex] ? 'bg-yellow-50' : '' }}">{{ strtoupper($vendor['quality'] ?? '-') }}</td>
                    @endforeach
                </tr>
                <tr class="text-left bg-white font-bold text-[9px]">
                    <td colspan="7" class="text-right px-2 py-1 border-r border-black border-t border-gray-300">PAYMENT</td>
                    @foreach($vendors as $vIndex => $vendor)
                        <td colspan="2" class="px-2 border-r border-black border-t border-gray-300 {{ $vendorWins[$vIndex] ? 'bg-yellow-50' : '' }}">{{ strtoupper($vendor['payment'] ?? '-') }}</td>
                    @endforeach
                </tr>
                <tr class="text-left bg-white font-bold text-[9px]">
                    <td colspan="7" class="text-right px-2 py-1 border-r border-black border-t border-gray-300 border-b border-black">EXPERIENCE NON IPPI</td>
                    @foreach($vendors as $vIndex => $vendor)
                        <td colspan="2" class="px-2 border-r border-black border-t border-gray-300 border-b border-black {{ $vendorWins[$vIndex] ? 'bg-yellow-50' : '' }}">{{ strtoupper($vendor['experience'] ?? '-') }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>

        <!-- FOOTER (Kiri: Prepared & Matrix, Kanan: QR & Signatures) -->
        <div class="flex justify-between items-end mt-auto gap-4">
            
            <div class="flex flex-col gap-4">
                <!-- Prepared / Negosiator / Menyetujui & Vendor Table -->
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-[10px]">
                        <span class="w-24 font-bold">PREPARED BY</span> <span class="font-bold">:</span> <span>Mahfud Sidik, Dimas Fery</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px]">
                        <span class="w-24 font-bold">NEGOSIATOR</span> <span class="font-bold">:</span> <span>Bramansyah Badar</span>
                    </div>
                    <div class="flex items-center gap-2 text-[10px]">
                        <span class="w-24 font-bold">MENYETUJUI</span> <span class="font-bold">:</span> <span>Ahmad Fadillah R.</span>
                    </div>

                    <table class="ph-table w-72 mt-1">
                        <tr>
                            <th colspan="2" class="bg-gray-100 py-1">VENDOR / SUPPLIER YANG DIPILIH</th>
                        </tr>
                        <tr>
                            <td class="w-1/2 py-1">{{ $proposalHarga->selected_vendor_name ?? '-' }}</td>
                            <td class="w-1/2 text-right pr-2">Rp {{ number_format($proposalHarga->nominal_request, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-bold bg-gray-50">
                            <td class="text-right pr-2 py-1">TOTAL</td>
                            <td class="text-right pr-2">Rp {{ number_format($proposalHarga->nominal_request, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Matrix Approval (Pojok Kiri Bawah) -->
                <div class="border border-black p-2 w-72 text-[8px] leading-tight bg-gray-50">
                    <div class="font-bold mb-1 underline">KETERANGAN: Matrix Approval Pembelian</div>
                    <ul class="list-disc pl-3 m-0">
                        <li><= Rp 100.000.000 (Ka. Dept)</li>
                        <li>Rp 100.000.001 < x < Rp 600.000.000 (Direktur FA & HCGS)</li>
                        <li>> Rp 600.000.001 (President Direktur)</li>
                    </ul>
                </div>
            </div>

            <!-- Tanda Tangan & QR (Pojok Kanan Bawah) -->
            <div class="flex flex-col items-end">
                <div class="mb-1 mr-1">Bekasi, {{ \Carbon\Carbon::parse($proposalHarga->created_at)->locale('id')->translatedFormat('d F Y') }}</div>
                
                <div class="flex items-center gap-7">
                    <!-- QR Code -->
                    <div class="flex items-center justify-center bg-white">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('tracking.show', $proposalHarga->ppbj_id)) }}" alt="QR Approval" class="w-24 h-24 object-contain" />
                    </div>

                    @php
                        $approvalsByStep = $proposalHarga->approvals->keyBy('step');
                        $nominalPH = (float) $proposalHarga->nominal_request;
                        $maxStepPH = 4; // Default sampai Ka Div (<= 100.000.000)
                        if ($nominalPH > 100000000 && $nominalPH <= 600000000) {
                            $maxStepPH = 5; // Sampai Direktur FA & HCGS
                        } elseif ($nominalPH > 600000000) {
                            $maxStepPH = 6; // Sampai Presdir
                        }
                    @endphp
                    <!-- Tanda Tangan Grid -->
                    <table class="ph-table">
                        <tr>
                            <th class="w-24 py-1">PREPARED</th>
                            <th class="w-24 py-1">CHECKED</th>
                            <th colspan="4" class="py-1">APPROVED</th>
                        </tr>
                        <tr>
                            @for($i = 1; $i <= 6; $i++)
                            <td class="h-24 align-middle border-b-0 w-24 px-1 p-0 relative overflow-hidden">
                                @if($i > $maxStepPH)
                                    <!-- Crossed out if not required -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                                        <svg class="w-full h-full text-black" viewBox="0 0 100 100" preserveAspectRatio="none">
                                            <line x1="0" y1="0" x2="100" y2="100" stroke="currentColor" stroke-width="1.5" />
                                            <line x1="100" y1="0" x2="0" y2="100" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </div>
                                @elseif($approval = $approvalsByStep->get($i))
                                    @if($approval->action === 'approved' && $approval->signature_data)
                                        <div class="w-full h-full flex items-center justify-center">
                                            <img src="{{ $approval->signature_data }}" alt="TTD" style="height:40px; transform: scale(1.8); transform-origin: center;">
                                        </div>
                                    @elseif($approval->action === 'rejected')
                                        <span class="text-rose-600 text-[8px] font-bold">DITOLAK</span>
                                    @endif
                                @elseif($proposalHarga->approval_step === $i && $proposalHarga->status === 'In_Review')
                                    <span class="text-amber-600 text-[8px]">Menunggu</span>
                                @endif
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="font-bold border-t-0 text-[8px]">Ka. Sie Purc.</td>
                            <td class="font-bold border-t-0 text-[8px]">Ka. Sie Proc.</td>
                            <td class="font-bold border-t-0 text-[8px]">Ka. Dept Proc & Import</td>
                            <td class="font-bold border-t-0 text-[8px]">Ka. Div FA & Proc</td>
                            <td class="font-bold border-t-0 text-[8px]">Direktur FA & HCGS</td>
                            <td class="font-bold border-t-0 text-[8px]">Presiden Direktur</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
