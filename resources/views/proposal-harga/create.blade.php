@extends('layouts.app')
@section('title', 'Buat Pengajuan — Step 2')
@section('page-title', 'Pengajuan Baru')
@section('page-subtitle', 'Step 2 dari 3 — Proposal Harga')

@section('content')
<div class="mb-6 animate-page">
    <div class="flex items-center gap-2">
        @foreach(['PPBJ', 'Proposal Harga', 'Internal Agreement'] as $i => $step)
        <div class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}">
            <div @class([
                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold shrink-0',
                'bg-indigo-600 text-white'    => $loop->index <= 1,
                'bg-slate-100 text-slate-400' => $loop->index > 1,
            ])>
                @if($loop->index === 0)
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                @else
                {{ $loop->iteration }}
                @endif
            </div>
            <span @class([
                'text-xs font-medium hidden sm:block',
                'text-indigo-600' => $loop->index === 1,
                'text-slate-400'  => $loop->index !== 1,
            ])>{{ $step }}</span>
            @if(!$loop->last)
            <div class="flex-1 h-px {{ $loop->index === 0 ? 'bg-indigo-300' : 'bg-slate-200' }} mx-1"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="max-w-5xl mx-auto animate-page-delay-1" x-data="phForm()">
    <form method="POST" action="{{ route('pengajuan.store-ph', $ppbj->id) }}" id="form-ph">
        @csrf
        <input type="hidden" name="items_data" :value="JSON.stringify(items)">
        <input type="hidden" name="nominal_request" :value="calculateTotalNominal()">
        <input type="hidden" name="cost_center_id" :value="selectedCostCenterId">

        <!-- Section A: Header Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
            <h3 class="text-base font-semibold text-slate-800 mb-4 border-b pb-2">Section A: Header Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">No. PPBJ</label>
                    <input type="text" value="{{ $ppbj->ppbj_number }}" readonly class="w-full px-3 py-2 rounded-lg border bg-slate-100 text-sm text-slate-600 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal Register</label>
                    <input type="text" value="{{ now()->format('d M Y') }}" readonly class="w-full px-3 py-2 rounded-lg border bg-slate-100 text-sm text-slate-600 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all outline-none">
                        <option value="SPAREPART" {{ old('type') == 'SPAREPART' ? 'selected' : '' }}>SPAREPART</option>
                        <option value="MATERIAL" {{ old('type') == 'MATERIAL' ? 'selected' : '' }}>MATERIAL</option>
                        <option value="JASA" {{ old('type') == 'JASA' ? 'selected' : '' }}>JASA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Divisi/Dept</label>
                    <input type="text" name="department_name" value="{{ old('department_name', $ppbj->user->department?->dept_name) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Seksi</label>
                    <input type="text" name="section_name" value="{{ old('section_name', $ppbj->user->section ?? '-') }}" readonly class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-sm text-slate-600 cursor-not-allowed outline-none">
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Cost Center (Pilih Cost Center sesuai departemen yang mengajukan) <span class="text-rose-500">*</span></label>
                    @if(isset($userCostCenter) && $userCostCenter)
                        <input type="text" value="{{ $userCostCenter->dropdown_label }}" readonly class="w-full px-3 py-2 rounded-lg border bg-indigo-50 text-sm text-indigo-700 font-medium outline-none cursor-not-allowed">
                        <input type="hidden" name="cost_center" value="{{ $userCostCenter->dropdown_label }}">
                        <input type="hidden" name="cost_center_id" value="{{ $userCostCenter->id }}">
                    @else
                        <select x-model="selectedCostCenterId" @change="onCostCenterChanged()" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all outline-none">
                            <option value="">— Pilih Cost Center —</option>
                            <template x-for="cc in costCenterOptions" :key="cc.id">
                                <option :value="cc.id" x-text="cc.label"></option>
                            </template>
                        </select>
                        <input type="hidden" name="cost_center" :value="selectedCostCenterLabel">
                        <input type="hidden" name="cost_center_id" :value="selectedCostCenterId">
                    @endif
                    @error('cost_center_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">No. IO/Asset</label>
                    <input type="text" name="no_io_asset" value="{{ old('no_io_asset') }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all outline-none">
                </div>

                {{-- Budget Info dari Cost Center yang dipilih --}}
                <div class="md:col-span-3" x-show="costCenterBudgetInfo" x-cloak>
                    <div class="bg-gradient-to-r from-indigo-50 to-emerald-50 rounded-xl p-4 border border-indigo-100">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-semibold text-indigo-700">Informasi Pagu Cost Center</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-500">Total Pagu:</span>
                                <span class="font-bold text-slate-800 ml-1" x-text="costCenterBudgetInfo?.plan ?? '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500">Terpakai:</span>
                                <span class="font-bold text-indigo-600 ml-1" x-text="costCenterBudgetInfo?.used ?? '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500">Sisa:</span>
                                <span class="font-bold text-emerald-600 ml-1" x-text="costCenterBudgetInfo?.remaining ?? '-'"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject', $ppbj->subject) }}" required class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all outline-none">
                </div>
            </div>
        </div>

        <!-- Section B: Dynamic Rows -->
        <!-- Unified Section B: Item & Vendor Bidding -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-base font-semibold text-slate-800">Section B: Item & Vendor Bidding</h3>
                <div class="flex gap-2">
                    <button type="button" @click="addVendor()" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg text-xs font-medium transition-colors">+ Add Vendor</button>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-medium transition-colors">+ Add Item</button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-max text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-200">
                            <th class="p-2 text-xs font-semibold text-slate-600 w-10 border-r border-slate-200" rowspan="2">No</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 border-r border-slate-200" rowspan="2">Description</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 w-20 border-r border-slate-200" rowspan="2">QTY</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 w-20 border-r border-slate-200" rowspan="2">UOM</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 text-center border-r border-slate-200" colspan="3">Last Order</th>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vh_'+vIndex">
                                <th class="p-2 text-xs font-semibold text-slate-600 text-center border-r border-slate-200 bg-emerald-50/50" colspan="2">
                                    <div class="flex items-center gap-1 mb-1">
                                        <input type="text" x-model="vendor.name" placeholder="Vendor Name" class="w-full px-2 py-1 border border-slate-200 rounded text-xs bg-white focus:border-indigo-400 outline-none text-center">
                                        <button type="button" @click="removeVendor(vIndex)" class="text-rose-400 hover:text-rose-600 font-bold px-1" x-show="vendors.length > 1" title="Hapus Vendor">&times;</button>
                                    </div>
                                    <button type="button" @click="setAllItemsToVendor(vIndex)" class="mt-1 w-full text-[10px] py-1 font-medium text-indigo-600 bg-white border border-indigo-200 rounded hover:bg-indigo-50 transition-colors">
                                        Set Pemenang Semua Item
                                    </button>
                                </th>
                            </template>
                            <th class="p-2 w-10" rowspan="2"></th>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-2 text-xs font-semibold text-slate-600 border-r border-slate-200">Price</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 border-r border-slate-200">Vendor</th>
                            <th class="p-2 text-xs font-semibold text-slate-600 border-r border-slate-200">Year</th>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vh2_'+vIndex">
                                <th colspan="2" class="p-0 border-r border-slate-200">
                                    <div class="grid grid-cols-2 h-full min-w-[200px]">
                                        <div class="p-2 text-xs font-semibold text-slate-600 bg-emerald-50/50 border-r border-slate-200">Unit Price</div>
                                        <div class="p-2 text-xs font-semibold text-slate-600 bg-emerald-50/50 text-right">Total</div>
                                    </div>
                                </th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-slate-100 group hover:bg-slate-50">
                                <td class="p-2 text-sm text-slate-600 text-center border-r border-slate-100" x-text="index + 1"></td>
                                <td class="p-2 border-r border-slate-100"><input type="text" x-model="item.description" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none"></td>
                                <td class="p-2 border-r border-slate-100"><input type="number" x-model="item.qty" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none"></td>
                                <td class="p-2 border-r border-slate-100"><input type="text" x-model="item.uom" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none"></td>
                                <td class="p-2 border-r border-slate-100"><input type="number" x-model="item.last_price" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none w-24"></td>
                                <td class="p-2 border-r border-slate-100"><input type="text" x-model="item.last_vendor" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none w-24"></td>
                                <td class="p-2 border-r border-slate-100"><input type="text" x-model="item.last_year" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none w-16"></td>
                                
                                <template x-for="(vendor, vIndex) in vendors" :key="'vp_'+index+'_'+vIndex">
                                    <td colspan="2" class="p-0 border-r border-slate-100" :class="item.winner_index == vIndex ? 'bg-yellow-50' : 'bg-emerald-50/20'">
                                        <div class="grid grid-cols-2 h-full items-center min-w-[200px]">
                                            <div class="p-2 border-r border-slate-100 flex items-center gap-2">
                                                <input type="radio" :name="'winner_'+index" :value="vIndex" x-model="item.winner_index" class="w-3.5 h-3.5 text-indigo-600 focus:ring-indigo-500 cursor-pointer" title="Pilih vendor ini sebagai pemenang item ini">
                                                <input type="number" x-model="item.vendor_prices[vIndex]" class="w-full px-2 py-1 border border-slate-200 rounded text-sm bg-white focus:border-indigo-400 outline-none">
                                            </div>
                                            <div class="p-2 text-sm font-medium text-slate-700 text-right break-all" :class="item.winner_index == vIndex ? 'text-indigo-700 font-bold' : ''" x-text="formatCurrency((item.qty || 0) * (item.vendor_prices[vIndex] || 0))"></div>
                                        </div>
                                    </td>
                                </template>
                                
                                <td class="p-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-rose-400 hover:text-rose-600 transition-colors" x-show="items.length > 1">
                                        <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Summary Row -->
                        <tr class="bg-indigo-50/50 font-bold border-y border-slate-200">
                            <td colspan="2" class="p-2 text-right text-xs text-slate-700 border-r border-slate-200">SUMMARY PURCHASE ORDER</td>
                            <td class="p-2 text-center text-xs text-indigo-700 border-r border-slate-200" x-text="calculateTotalQty() + ' item'"></td>
                            <td colspan="4" class="p-2 text-right text-xs text-slate-700 border-r border-slate-200">
                                <div>TOTAL ESTIMASI:</div>
                                <div class="text-indigo-700 mt-0.5" x-text="formatCurrency(calculateTotalNominal())"></div>
                            </td>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vtotal_'+vIndex">
                                <td colspan="2" class="p-0 border-r border-slate-200 bg-white">
                                    <div class="flex flex-col h-full items-center justify-center p-2 min-w-[200px]">
                                        <div class="text-[9px] text-slate-500 uppercase mb-0.5">Total Dimenangkan</div>
                                        <div class="text-indigo-700 font-bold text-sm" x-text="formatCurrency(calculateVendorWinningTotal(vIndex))"></div>
                                    </div>
                                </td>
                            </template>
                            <td></td>
                        </tr>

                        <!-- Term & Conditions -->
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <td colspan="7" class="p-2 text-right text-xs font-semibold text-slate-600 border-r border-slate-200">Delivery</td>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vdel_'+vIndex">
                                <td colspan="2" class="p-2 border-r border-slate-200">
                                    <input type="text" x-model="vendor.delivery" class="w-full px-2 py-1 border border-slate-200 rounded text-xs bg-white focus:border-indigo-400 outline-none" placeholder="ex: 3 DAYS">
                                </td>
                            </template>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <td colspan="7" class="p-2 text-right text-xs font-semibold text-slate-600 border-r border-slate-200">Quality</td>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vqual_'+vIndex">
                                <td colspan="2" class="p-2 border-r border-slate-200">
                                    <input type="text" x-model="vendor.quality" class="w-full px-2 py-1 border border-slate-200 rounded text-xs bg-white focus:border-indigo-400 outline-none" placeholder="ex: NO PROBLEM">
                                </td>
                            </template>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <td colspan="7" class="p-2 text-right text-xs font-semibold text-slate-600 border-r border-slate-200">Payment</td>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vpay_'+vIndex">
                                <td colspan="2" class="p-2 border-r border-slate-200">
                                    <input type="text" x-model="vendor.payment" class="w-full px-2 py-1 border border-slate-200 rounded text-xs bg-white focus:border-indigo-400 outline-none" placeholder="ex: 45 DAYS AFTER INVOICE">
                                </td>
                            </template>
                            <td></td>
                        </tr>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <td colspan="7" class="p-2 text-right text-xs font-semibold text-slate-600 border-r border-slate-200">Experience Non IPPI</td>
                            <template x-for="(vendor, vIndex) in vendors" :key="'vexp_'+vIndex">
                                <td colspan="2" class="p-2 border-r border-slate-200">
                                    <input type="text" x-model="vendor.experience" class="w-full px-2 py-1 border border-slate-200 rounded text-xs bg-white focus:border-indigo-400 outline-none">
                                </td>
                            </template>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-between items-center bg-indigo-50/50 p-4 rounded-lg border border-indigo-100">
                <div class="text-sm text-indigo-700">
                    <span class="font-medium">Vendor Pemenang:</span>
                    <span class="font-bold ml-1" x-text="getWinningVendorNames() || 'Belum dipilih'"></span>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500 mb-1">Total Nominal Request (Semua Item Dimenangkan):</p>
                    <p class="text-lg font-bold text-indigo-600" x-text="formatCurrency(calculateTotalNominal())"></p>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for backend compatibility -->
        <input type="hidden" name="items_data" :value="getCompiledItemsData()">
        <input type="hidden" name="nominal_request" :value="calculateTotalNominal()">
        <input type="hidden" name="selected_vendor_name" :value="getWinningVendorNames()">
        <input type="hidden" name="delivery_time" :value="vendors[getFirstWinningVendorIndex()]?.delivery || ''">
        <input type="hidden" name="quality" :value="vendors[getFirstWinningVendorIndex()]?.quality || ''">
        <input type="hidden" name="payment_terms" :value="vendors[getFirstWinningVendorIndex()]?.payment || ''">
        <input type="hidden" name="experience_non_ippi" :value="vendors[getFirstWinningVendorIndex()]?.experience || ''">

        <div class="flex gap-4">
            <a href="{{ route('pengajuan.index') }}" class="py-3 px-6 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition-all text-center">
                Batal / Kembali
            </a>
            <button type="submit" class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-all shadow-lg shadow-indigo-500/25">
                Simpan & Lanjut ke Internal Agreement →
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Load Alpine.js for dynamic features -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
    $oldItemsData = old('items_data') ? json_decode(old('items_data'), true) : null;
    if ($oldItemsData && isset($oldItemsData['vendors']) && isset($oldItemsData['items'])) {
        $defaultVendorsJson = json_encode($oldItemsData['vendors']);
        $defaultItemsJson = json_encode($oldItemsData['items']);
    } else {
        $defaultVendorsJson = json_encode([
            [ 'name' => '', 'delivery' => '', 'quality' => '', 'payment' => '', 'experience' => '' ]
        ]);
        $defaultItemsJson = json_encode([
            [
                'description' => $ppbj->nama_barang_jasa ?? '',
                'qty' => (int) ($ppbj->qty ?? 1),
                'uom' => $ppbj->uom ?? '',
                'last_price' => 0,
                'last_vendor' => '',
                'last_year' => '',
                'vendor_prices' => [0],
                'winner_index' => '0'
            ]
        ]);
    }
@endphp
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('phForm', () => ({
        vendors: {!! $defaultVendorsJson !!},
        items: {!! $defaultItemsJson !!},

        // Cost Center dropdown state
        selectedCostCenterId: '{{ old('cost_center_id', isset($userCostCenter) && $userCostCenter ? $userCostCenter->id : '') }}',
        selectedCostCenterLabel: '{{ old('cost_center', isset($userCostCenter) && $userCostCenter ? $userCostCenter->dropdown_label : '') }}',
        costCenterOptions: [
            @foreach($costCenters as $cc)
                { id: {{ $cc->id }}, label: "{!! addslashes($cc->dropdown_label) !!}" },
            @endforeach
        ],
        costCenterBudgetInfo: null,

        init() {
            if (this.selectedCostCenterId) {
                this.loadBudgetInfo(this.selectedCostCenterId);
            }
        },

        onCostCenterChanged() {
            const cc = this.costCenterOptions.find(c => c.id == this.selectedCostCenterId);
            this.selectedCostCenterLabel = cc ? cc.label : '';
            if (this.selectedCostCenterId) {
                this.loadBudgetInfo(this.selectedCostCenterId);
            } else {
                this.costCenterBudgetInfo = null;
            }
        },

        async loadBudgetInfo(ccId) {
            try {
                const resp = await fetch(`/api/cost-centers/budget-info?id=${ccId}`);
                const data = await resp.json();
                this.costCenterBudgetInfo = {
                    plan: data.plan_formatted || 'Rp 0',
                    used: data.used_formatted || 'Rp 0',
                    remaining: data.remaining_formatted || 'Rp 0',
                };
            } catch(e) {
                console.error('Failed to load budget info:', e);
                this.costCenterBudgetInfo = null;
            }
        },

        addVendor() {
            this.vendors.push({ name: '', delivery: '', quality: '', payment: '', experience: '' });
            this.items.forEach(item => {
                item.vendor_prices.push(0);
            });
        },
        removeVendor(index) {
            if (this.vendors.length <= 1) return;
            this.vendors.splice(index, 1);
            this.items.forEach(item => {
                item.vendor_prices.splice(index, 1);
                if (item.winner_index == index) {
                    item.winner_index = '0';
                } else if (item.winner_index > index) {
                    item.winner_index--;
                }
            });
        },
        setAllItemsToVendor(index) {
            this.items.forEach(item => item.winner_index = index);
        },
        addItem() {
            const prices = this.vendors.map(() => 0);
            this.items.push({ description: '', qty: 1, uom: '', last_price: 0, last_vendor: '', last_year: '', vendor_prices: prices, winner_index: '0' });
        },
        removeItem(index) {
            if (this.items.length <= 1) return;
            this.items.splice(index, 1);
        },
        calculateVendorTotal(vIndex) {
            return this.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.vendor_prices[vIndex]) || 0)), 0);
        },
        calculateVendorWinningTotal(vIndex) {
            return this.items.reduce((sum, item) => {
                if (item.winner_index == vIndex) {
                    return sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.vendor_prices[vIndex]) || 0));
                }
                return sum;
            }, 0);
        },
        calculateTotalNominal() {
            return this.vendors.reduce((sum, vendor, vIndex) => sum + this.calculateVendorWinningTotal(vIndex), 0);
        },
        getWinningVendorNames() {
            const winningIndices = new Set(this.items.map(i => i.winner_index));
            return Array.from(winningIndices).map(idx => this.vendors[idx]?.name).filter(Boolean).join(', ');
        },
        getFirstWinningVendorIndex() {
            const idx = this.items[0]?.winner_index ?? '0';
            return parseInt(idx);
        },
        calculateTotalQty() {
            return this.items.reduce((sum, item) => sum + parseInt(item.qty || 0), 0);
        },
        getCompiledItemsData() {
            return JSON.stringify({
                vendors: this.vendors,
                items: this.items,
                summary_qty: this.calculateTotalQty()
            });
        },
        formatCurrency(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
        }
    }));
});
</script>
@endpush
