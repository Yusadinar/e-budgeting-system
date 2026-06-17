@extends('layouts.app')
@section('title', 'Cetak Internal Agreement')
@section('page-title', 'Internal Agreement')
@section('page-subtitle', $ia->ia_number)

@push('styles')
<style>
@media print {
    @page { size: A4 landscape; margin: 10mm; }
    body { background:#fff!important; zoom: 95%; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #sidebar, #sidebar-overlay, header, footer, .no-print { display:none!important; }
    #main-content { margin:0!important; }
    main { padding:0!important; }
    .paper-wrapper { overflow: visible!important; }
    .paper { box-shadow:none!important; border:none!important; padding: 8mm!important; margin: 0!important; width: 100%!important; box-sizing: border-box!important; min-height: auto!important; display: block!important; }
    .paper::before { display: none; }
}
.paper-wrapper { overflow-x: auto; padding-bottom: 24px; }
.paper {
    background: #fff; width: 297mm; min-height: 210mm; margin: 0 auto;
    border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 4px 24px rgba(0,0,0,.08);
    padding: 24px 32px; position: relative; display: flex; flex-direction: column;
}
.paper::before { content:''; position:absolute; top:0; left:0; right:0; height:6px; background:linear-gradient(90deg,#4f46e5,#6366f1,#818cf8); border-radius:4px 4px 0 0; }
.ia-table { border-collapse: collapse; width: 100%; text-align: center; font-size: 10px; border: 1px solid #000; }
.ia-table th, .ia-table td { border: 1px solid #000; padding: 4px 6px; }
.ia-table th { background-color: #f3f4f6; }
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
        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold tracking-widest uppercase shadow-sm border border-black/5" style="{{ $ia->status_ia === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($ia->status_ia === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $ia->status_ia ?? 'Draft') }}
        </span>
    </div>

    <div class="flex items-center gap-3">
        <span class="sm:hidden inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm" style="{{ $ia->status_ia === 'Approved' ? 'background:#dcfce7;color:#16a34a;' : ($ia->status_ia === 'Rejected' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fef9c3;color:#ca8a04;') }}">
            {{ str_replace('_', ' ', $ia->status_ia ?? 'Draft') }}
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

<div class="animate-page-delay-1">
    <div class="paper-wrapper">
        <div class="paper text-black text-[11px]" style="font-family: Arial, Helvetica, sans-serif;">
            <div class="border-[1.5px] border-black p-5 flex-1 flex flex-col">
                
                <!-- HEADER -->
                <div class="flex justify-between items-start mb-6">
                    <!-- Header Kiri (Border Box) -->
                    <div class="w-[30%]">
                        <div class="flex items-center gap-2 mb-1">
                            <img src="{{ asset('images/ippi-logo.png') }}" alt="Logo" class="h-10 object-contain" />
                            <div>
                                <h1 class="font-bold text-[11px] leading-tight">INTI PANTJA<br>PRESS INDUSTRI</h1>
                            </div>
                        </div>
                        
                        <!-- Box Detail Kiri -->
                        <div class="border border-black text-[9px] mt-1 leading-[1.0]">
                            <div class="p-1 border-b border-black">
                                <table class="w-full">
                                    <tr><td class="w-[95px] whitespace-nowrap">DIVISI/DEPT</td><td class="uppercase">: {{ $ia->proposalHarga->ppbj->user->department?->dept_name ?? '-' }}</td></tr>
                                    <tr><td>SECTION</td><td class="uppercase">: {{ $ia->proposalHarga->section_name ?? '-' }}</td></tr>
                                    <tr><td>TGL. REG. SECT.</td><td>: {{ $ia->created_at->format('d/m/Y') }}</td></tr>
                                    <tr><td>TGL. REG. BGT.</td><td>: {{ $ia->created_at->format('d/m/Y') }}</td></tr>
                                </table>
                            </div>
                            <div class="p-1">
                                <table class="w-full">
                                    <tr><td class="w-[95px] whitespace-nowrap">COST CENTER</td><td>: {{ trim(explode(' ', $ia->proposalHarga->cost_center ?? '-')[0]) }}</td></tr>
                                    <tr><td class="whitespace-nowrap">NO. FR/IO/MATNUM</td><td>: &nbsp;</td></tr>
                                    <tr><td>NO. ASSET</td><td>: &nbsp;</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Header Tengah -->
                    <div class="w-[40%] text-center px-4">
                        <h2 class="font-bold text-lg uppercase underline decoration-2 underline-offset-4 mb-2 mt-0">Internal Agreement</h2>
                        <p class="font-bold text-sm">NO. : {{ $ia->ia_number }}</p>
                        
                        <!-- QR Placement -->
                        <div class="flex justify-center mt-4">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('tracking.show', $ia->proposalHarga->ppbj->id)) }}" alt="QR Code" class="w-14 h-14 object-cover" />
                        </div>
                        <div class="text-[8px] mt-1 font-mono text-gray-500 tracking-tight">SCAN FOR TRACKING</div>
                    </div>

                    <!-- Header Kanan (Border Box) -->
                    <div class="w-[30%] relative">
                        <!-- Form Code -->
                        <div class="absolute top-0 right-0 text-[8px] font-bold">FPSM-BGT-01-01</div>
                        
                        <!-- Spacer agar sejajar dengan Box Detail Kiri -->
                        <div class="h-[44px]"></div>
                        <div class="border border-black text-[9px] leading-[1.0]">
                            <!-- Kotak Atas -->
                            <div class="p-1 border-b border-black text-center">
                                <div class="font-bold">SUPPLIER/CONTRACTOR YANG DIPILIH:</div>
                                <div class="uppercase px-1 font-bold mt-1">{{ $ia->proposalHarga->selected_vendor_name }}</div>
                            </div>
                            <!-- Kotak Bawah -->
                            <div class="p-1">
                                <div class="font-bold mb-0.5">HASIL NEGOSIASI HARGA</div>
                                <table class="w-full leading-[1.0]">
                                    <tr><td class="w-14">NO.</td><td>: {{ $ia->sap_doc_no ?? '-' }}</td></tr>
                                    <tr><td>TANGGAL</td><td>: {{ $ia->created_at->locale('id')->translatedFormat('d F Y') }}</td></tr>
                                    <tr>
                                        <td class="align-middle">NILAI</td>
                                        <td class="font-bold">: Rp {{ number_format($ia->final_nominal, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL UTAMA -->
                <div class="mb-4">
                    <div class="font-bold text-[10px] mb-1">MOHON AGAR DAPAT DISETUJUI PERMOHONAN DIBAWAH INI:</div>
                    <table class="ia-table">
                        <thead>
                            <tr>
                                <th class="w-6">NO</th>
                                <th>JENIS PERMINTAAN</th>
                                <th class="w-16">JUMLAH</th>
                                <th class="w-24">HARGA</th>
                                <th class="w-24">TOTAL HARGA</th>
                                <th class="w-48">ALASAN KEBUTUHAN / LAMPIRAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris Pertama: Judul PH -->
                            <tr>
                                <td class="text-center font-bold"></td>
                                <td class="text-left px-2 font-bold">{{ $ia->proposalHarga->subject }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-left px-2 align-top text-[9px] leading-[1.2]">
                                    <div class="font-bold">REF:</div>
                                    <div>PPBJ: {{ $ia->proposalHarga->ppbj->ppbj_number }}</div>
                                    <div>PH: {{ $ia->proposalHarga->ph_number }}</div>
                                </td>
                            </tr>
                            
                            @php
                                $itemsData = is_string($ia->proposalHarga->items_data) ? json_decode($ia->proposalHarga->items_data, true) : $ia->proposalHarga->items_data;
                                
                                $isNewFormat = isset($itemsData['vendors']) && isset($itemsData['items']);
                                if ($isNewFormat) {
                                    $items = $itemsData['items'];
                                } else {
                                    $items = is_array($itemsData) ? $itemsData : [];
                                    if (!empty($items) && !isset($items[0])) {
                                        $items = [$items];
                                    }
                                }
                                
                                $totalEstimasi = 0;
                            @endphp
                            @forelse($items as $item)
                            @php
                                $rawQty = $item['qty'] ?? 0;
                                $winnerIdx = $item['winner_index'] ?? 0;
                                $rawPrice = $item['vendor_prices'][$winnerIdx] ?? ($item['unit_price'] ?? 0);
                                
                                $qty = is_numeric($rawQty) ? (float) $rawQty : 0;
                                $price = is_numeric($rawPrice) ? (float) $rawPrice : 0;
                                $totalHarga = $qty * $price;
                                $totalEstimasi += $totalHarga;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-left px-2 pl-4">{{ $item['description'] ?? '-' }}</td>
                                <td class="text-right px-2">{{ $rawQty }} {{ $item['uom'] ?? '' }}</td>
                                <td class="text-right px-2">{{ number_format($price, 0, ',', '.') }}</td>
                                <td class="text-right px-2">{{ number_format($totalHarga, 0, ',', '.') }}</td>
                                <td class="text-left px-2 text-[9px]">-</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center italic text-gray-400 py-2">Data item tidak ditemukan</td></tr>
                            @endforelse
                            
                            @php
                                $emptyRows = max(0, 5 - count($items));
                            @endphp
                            @for($i = 0; $i < $emptyRows; $i++)
                            <tr>
                                <td class="text-center h-5">&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-center font-bold px-2 py-1.5">TOTAL ESTIMASI</td>
                                <td class="text-right font-bold px-2 py-1.5">{{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                                <td class="border-l-0"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center font-bold px-2 py-1.5">NILAI FINAL NEGOSIASI</td>
                                <td class="text-right font-bold px-2 py-1.5 text-indigo-700">Rp {{ number_format($ia->final_nominal, 0, ',', '.') }}</td>
                                <td class="border-l-0 text-left font-bold px-2 text-indigo-700 italic text-[9px]">Hasil Negosiasi</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- FOOTER GRID TANDA TANGAN & REGISTRASI -->
                <div class="mt-auto pt-4">
                    <table class="ia-table w-full">
                        <tr>
                            <th class="py-1">PEMOHON</th>
                            <th colspan="5" class="py-1">DISETUJUI OLEH</th>
                            <th class="py-1 w-40">KETERANGAN</th>
                        </tr>
                        <tr>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Ka. Sie terkait</th>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Ka. Dept terkait</th>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Ka. Div terkait</th>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Ka. Dept FA</th>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Production Director</th>
                            <th class="w-[14%] text-[8px] font-normal py-0.5">Finance Director</th>
                            <td rowspan="3" class="align-top text-left p-2 text-[9px] font-normal bg-white">
                                <div class="flex items-start mb-0.5">
                                    <div class="w-[55px] font-bold">NO REGIS</div>
                                    <div class="font-bold">: {{ explode('/', $ia->ia_number)[0] }}/{{ $ia->created_at->format('Y') }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-[55px] font-bold">TGL REGIS</div>
                                    <div class="font-bold">: {{ $ia->created_at->format('d/m/Y') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            @php
                                $nominalIA = (float) $ia->final_nominal;
                                $maxStepIA = 4;
                                if ($nominalIA > 100000000 && $nominalIA <= 600000000) $maxStepIA = 5;
                                elseif ($nominalIA > 600000000) $maxStepIA = 6;
                            @endphp
                            @for($step = 1; $step <= 6; $step++)
                                @php
                                    $approval = $ia->approvals->where('step', $step)->first();
                                @endphp
                                <td class="h-24 align-middle p-0 relative overflow-hidden bg-white">
                                    @if($step > $maxStepIA)
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                                            <svg class="w-full h-full text-black" viewBox="0 0 100 100" preserveAspectRatio="none">
                                                <line x1="0" y1="0" x2="100" y2="100" stroke="currentColor" stroke-width="1.5" />
                                                <line x1="100" y1="0" x2="0" y2="100" stroke="currentColor" stroke-width="1.5" />
                                            </svg>
                                        </div>
                                    @elseif($approval && $approval->action === 'approved')
                                        @if($approval->signature_data)
                                            <img src="{{ $approval->signature_data }}" class="absolute inset-0 w-full h-full object-contain p-1 mix-blend-multiply opacity-80" alt="Signature">
                                        @else
                                            <span class="text-emerald-500 font-bold text-[10px] break-words">APPROVED</span>
                                        @endif
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        <tr>
                            @for($step = 1; $step <= 6; $step++)
                                @php
                                    $approval = $ia->approvals->where('step', $step)->first();
                                @endphp
                                <td class="border-t-0 text-[8px] pt-1">
                                    {{ $approval ? $approval->user->name : '' }}
                                </td>
                            @endfor
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
