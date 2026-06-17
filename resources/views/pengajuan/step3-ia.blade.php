@extends('layouts.app')
@section('title', 'Buat Pengajuan — Step 3')
@section('page-title', 'Pengajuan Baru')
@section('page-subtitle', 'Step 3 dari 3 — Internal Agreement')

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
.cell-input { width:100%; border:none; border-bottom:1px dashed #cbd5e1; padding:2px 0; font-size:10px; background:transparent; outline:none; font-family:inherit; color:#000; transition:all 0.2s;}
.cell-input:focus { border-bottom-color:#6366f1; background-color:#eef2ff; }
.cell-input.no-border { border-bottom:none; text-align:left; }
.cell-input.no-border:focus { border-bottom:1px dashed #6366f1; background-color:#eef2ff; }
</style>
@endpush

@section('content')
<div class="mb-6 animate-page no-print">
    <div class="flex items-center gap-2">
        @foreach(['PPBJ', 'Proposal Harga', 'Internal Agreement'] as $i => $step)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0',
                'bg-indigo-600 text-white'    => true,
                'ring-2 ring-indigo-200'      => $loop->last,
            ])>
                @if(!$loop->last)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                @else
                {{ $loop->iteration }}
                @endif
            </div>
            <span class="text-xs font-medium hidden sm:block {{ $loop->last ? 'text-indigo-600' : 'text-slate-400' }}">{{ $step }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px bg-indigo-300 mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="animate-page-delay-1">
    <form method="POST" action="{{ route('pengajuan.store-ia', $proposalHarga->id) }}" id="form-ia">
        @csrf
        
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
                                        <tr><td class="w-[95px] whitespace-nowrap">DIVISI/DEPT</td><td class="uppercase">: {{ $proposalHarga->ppbj->user->department?->dept_name ?? '-' }}</td></tr>
                                        <tr><td>SECTION</td><td class="uppercase">: {{ $proposalHarga->section_name ?? '-' }}</td></tr>
                                        <tr><td>TGL. REG. SECT.</td><td>: {{ now()->format('d/m/Y') }}</td></tr>
                                        <tr><td>TGL. REG. BGT.</td><td>: {{ now()->format('d/m/Y') }}</td></tr>
                                    </table>
                                </div>
                                <div class="p-1">
                                    <table class="w-full">
                                        <tr><td class="w-[95px] whitespace-nowrap">COST CENTER</td><td>: {{ trim(explode(' ', $proposalHarga->cost_center ?? '-')[0]) }}</td></tr>
                                        <tr><td class="whitespace-nowrap">NO. FR/IO/MATNUM</td><td>: &nbsp;</td></tr>
                                        <tr><td>NO. ASSET</td><td>: &nbsp;</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Header Tengah -->
                        <div class="w-[40%] text-center px-4">
                            <h2 class="font-bold text-lg uppercase underline decoration-2 underline-offset-4 mb-2 mt-0">Internal Agreement</h2>
                            <p class="font-bold text-sm">NO. : <span class="text-gray-400 font-normal italic text-[11px]">(Auto Generated)</span></p>
                            
                            <!-- QR Placement (Optional as per PRD) -->
                            <div class="flex justify-center mt-4">
                                <div class="flex flex-col items-center justify-center p-1 border border-black w-14 h-14 bg-gray-50">
                                    <span class="text-[6px] text-gray-400 text-center italic leading-tight">QR Code<br>Generated<br>After Save</span>
                                </div>
                            </div>
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
                                    <div class="uppercase px-1 font-bold mt-1">{{ $proposalHarga->selected_vendor_name }}</div>
                                </div>
                                <!-- Kotak Bawah -->
                                <div class="p-1">
                                    <div class="font-bold mb-0.5">HASIL NEGOSIASI HARGA</div>
                                    <table class="w-full leading-[1.0]">
                                        <tr><td class="w-14">NO.</td><td>: &nbsp;</td></tr>
                                        <tr><td>TANGGAL</td><td>: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</td></tr>
                                        <tr>
                                            <td class="align-middle">NILAI</td>
                                            <td class="flex items-center">
                                                <span>:&nbsp;Rp&nbsp;</span>
                                                <input type="number" name="final_nominal" value="{{ old('final_nominal', $proposalHarga->nominal_request) }}" 
                                                    class="cell-input no-border text-left font-bold text-[10px] m-0 p-0 h-3 bg-indigo-50/50 flex-1" required min="1">
                                            </td>
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
                                    <td class="text-left px-2 font-bold">{{ $proposalHarga->subject }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-left px-2 align-top text-[9px] leading-[1.2]">
                                        <div class="font-bold">REF:</div>
                                        <div>PPBJ: {{ $proposalHarga->ppbj->ppbj_number }}</div>
                                        <div>PH: {{ $proposalHarga->ph_number }}</div>
                                    </td>
                                </tr>
                                
                                @php
                                    $itemsData = is_string($proposalHarga->items_data) ? json_decode($proposalHarga->items_data, true) : $proposalHarga->items_data;
                                    
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
                                    <td class="px-0 py-0"><input type="text" class="cell-input no-border w-full px-2 text-left bg-transparent" placeholder="Catatan..."></td>
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
                                    <td class="px-0 py-0"></td>
                                </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-center font-bold px-2 py-1.5">TOTAL</td>
                                    <td class="text-right font-bold px-2 py-1.5">{{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                                    <td class="text-left font-bold px-2 py-1.5 border-l-0 relative">
                                        <div class="flex items-center gap-1">
                                            <span>DISKON / TOTAL : </span>
                                            <span class="text-indigo-700 italic text-[9px]">(Tarik dr Nominal Final di atas)</span>
                                        </div>
                                    </td>
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
                                    <div class="flex items-start pb-1">
                                        <div class="w-[55px] font-bold">NO REGIS</div>
                                        <div class="font-bold">: &nbsp;</div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-[55px] font-bold">TGL REGIS</div>
                                        <div class="font-bold">: {{ now()->format('d/m/Y') }}</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                @php
                                    $nominalIA = (float) old('final_nominal', $proposalHarga->nominal_request);
                                    $maxStepIA = 4;
                                    if ($nominalIA > 100000000 && $nominalIA <= 600000000) $maxStepIA = 5;
                                    elseif ($nominalIA > 600000000) $maxStepIA = 6;
                                @endphp
                                @for($i = 1; $i <= 6; $i++)
                                <td class="h-16 align-middle p-0 relative overflow-hidden bg-gray-50 border-b-0">
                                    @if($i > $maxStepIA)
                                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; opacity: 0.2; pointer-events: none;">
                                            <svg style="width: 100%; height: 100%; color: black;" viewBox="0 0 100 100" preserveAspectRatio="none">
                                                <line x1="0" y1="0" x2="100" y2="100" stroke="currentColor" stroke-width="1.5" />
                                                <line x1="100" y1="0" x2="0" y2="100" stroke="currentColor" stroke-width="1.5" />
                                            </svg>
                                        </div>
                                    @elseif($i == 1)
                                        <span class="text-[7px] text-gray-400 italic">Tanda Tangan<br>di Bawah</span>
                                    @else
                                        <span class="text-[7px] text-gray-400 italic">Workflow</span>
                                    @endif
                                </td>
                                @endfor
                            </tr>
                            <tr>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                                <td class="border-t-0 text-[8px] pt-1">&nbsp;</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>

            <div class="no-print mt-8 flex justify-end gap-3 mx-auto max-w-5xl">
                <a href="{{ route('pengajuan.index') }}" class="px-6 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">Batal</a>
                <button type="submit" class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 shadow-md shadow-indigo-500/25 transition-all">Simpan & Ajukan Internal Agreement →</button>
            </div>
        </div>
    </form>
</div>
@endsection