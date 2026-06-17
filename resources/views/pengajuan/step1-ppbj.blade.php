@extends('layouts.app')
@section('title', 'Form PPBJ')
@section('page-title', 'Pengajuan PPBJ')
@section('page-subtitle', 'Permohonan Pengadaan Barang/Jasa')

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 5mm;
    }
    body { 
        background:#fff!important; 
        zoom: 70%; 
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact; 
    }
    #sidebar, #sidebar-overlay, header, footer, .no-print { display:none!important; }
    #main-content { margin:0!important; }
    main { padding:0!important; }
    .paper { box-shadow:none!important; border:none!important; margin:0!important; max-width:100%!important; padding:0!important; }
}
.paper {
    background:#fff; max-width:900px; margin:0 auto;
    border:1px solid #cbd5e1; border-radius:4px;
    box-shadow:0 4px 24px rgba(0,0,0,.08), 0 1px 4px rgba(0,0,0,.04);
    padding:32px 40px; position:relative;
    font-size:13px; color:#1e293b; line-height:1.5;
}
.paper::before {
    content:''; position:absolute; top:0; left:0; right:0; height:6px;
    background:linear-gradient(90deg,#4f46e5,#6366f1,#818cf8); border-radius:4px 4px 0 0;
}
.doc-title { text-align:center; font-size:16px; font-weight:700; letter-spacing:.5px; margin-bottom:2px; color:#1e1b4b; }
.doc-sub { text-align:center; font-size:11px; color:#64748b; margin-bottom:16px; }
.doc-number { text-align:center; font-size:13px; font-weight:600; color:#4f46e5; margin-bottom:20px; padding:6px 0; border:1px dashed #c7d2fe; background:#eef2ff; border-radius:4px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; border:1px solid #cbd5e1; }
.form-cell { padding:8px 12px; border:1px solid #e2e8f0; min-height:42px; }
.form-cell.full { grid-column:1/-1; }
.form-cell.left { grid-column:1; }
.form-cell.right { grid-column:2; }
.cell-label { font-size:11px; font-weight:600; color:#475569; margin-bottom:4px; display:flex; align-items:center; gap:2px; }
.cell-label .req { color:#ef4444; }
.cell-input { width:100%; border:none; border-bottom:1px solid #e2e8f0; padding:4px 0; font-size:12px; background:transparent; outline:none; font-family:inherit; color:#1e293b; }
.cell-input:focus { border-bottom-color:#6366f1; }
textarea.cell-input { resize:vertical; min-height:48px; border:1px solid #e2e8f0; border-radius:3px; padding:6px; }
select.cell-input { border:1px solid #e2e8f0; border-radius:3px; padding:4px 6px; cursor:pointer; }
.section-header { background:#f1f5f9; font-size:12px; font-weight:700; color:#334155; padding:8px 12px; text-transform:uppercase; letter-spacing:.5px; border:1px solid #e2e8f0; grid-column:1/-1; }
.radio-group { display:flex; flex-wrap:wrap; gap:8px; margin-top:4px; }
.radio-item { display:flex; align-items:center; gap:4px; font-size:12px; cursor:pointer; }
.radio-item input { accent-color:#4f46e5; }
.check-group { display:flex; flex-wrap:wrap; gap:10px; margin-top:4px; }
.check-item { display:flex; align-items:center; gap:4px; font-size:12px; cursor:pointer; }
.check-item input { accent-color:#4f46e5; }
.drop-zone { border:2px dashed #cbd5e1; border-radius:6px; padding:16px; text-align:center; cursor:pointer; transition:all .2s; background:#f8fafc; min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.drop-zone:hover { border-color:#6366f1; background:#eef2ff; }
.drop-zone.dragover { border-color:#4f46e5; background:#e0e7ff; }
.drop-zone img { max-height:120px; max-width:100%; object-fit:contain; border-radius:4px; margin-top:6px; }
.sign-table { width:100%; border-collapse:collapse; margin-top:16px; }
.sign-table td, .sign-table th { border:1px solid #e2e8f0; padding:6px 10px; font-size:11px; text-align:center; }
.sign-table th { background:#f1f5f9; font-weight:600; color:#334155; }
.sign-box { height:50px; }
.btn-submit { display:inline-flex; align-items:center; gap:8px; padding:10px 28px; background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(79,70,229,.3); }
.btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(79,70,229,.4); }
.btn-print { display:inline-flex; align-items:center; gap:6px; padding:8px 20px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:500; cursor:pointer; transition:all .15s; }
.btn-print:hover { background:#e2e8f0; }
@media(max-width:768px) {
    .paper { padding:16px; font-size:12px; }
    .form-grid { grid-template-columns:1fr; }
    .form-cell.left,.form-cell.right { grid-column:1; }
}
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('pengajuan.store-ppbj') }}" enctype="multipart/form-data" id="ppbjForm">
@csrf

<div class="paper animate-page">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <img src="{{ asset('images/ippi-logo.png') }}" alt="Logo" style="height:64px;">
        <div style="text-align:right;">
            <div style="font-size:10px;color:#64748b;">PT INTI PANTJA PRESS INDUSTRI</div>
            <button type="button" onclick="fillDummyData()" class="no-print" style="margin-top:4px; padding:4px 8px; background:#fef08a; color:#854d0e; border:1px solid #fde047; border-radius:4px; font-size:10px; cursor:pointer; font-weight:bold;">⚡ Auto-Fill Testing</button>
        </div>
    </div>
    <div class="doc-title">PERMOHONAN PENGADAAN BARANG / JASA (PPBJ)</div>
    <div class="doc-sub">Form Pengajuan Internal — E-Budgeting System</div>

    {{-- (1) No PPBJ --}}
    <div class="doc-number">No PPBJ: <span id="ppbjNumber">{{ $nextNumber }}</span></div>

    {{-- FORM GRID --}}
    <div class="form-grid">

        {{-- SECTION: Data Umum --}}
        <div class="section-header">A. Data Umum Pengajuan</div>

        {{-- (2) Kolom Kiri --}}
        <div class="form-cell left">
            <div class="cell-label">Department / Section <span class="req">*</span></div>
            <select name="department_section" class="cell-input" required>
                <option value="">— Pilih —</option>
                @foreach($departments as $id => $name)
                <option value="{{ $name }}" {{ old('department_section') == $name ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- (3) Kolom Kanan: IA No --}}
        <div class="form-cell right">
            <div class="cell-label">IA No. <span style="font-weight:400;color:#94a3b8;font-size:10px;">(diisi Accounting)</span></div>
            <input type="text" name="ia_no" class="cell-input" placeholder=" " value="{{ old('ia_no') }}">
        </div>

        <div class="form-cell left">
            <div class="cell-label">Subject <span class="req">*</span></div>
            <input type="text" name="subject" class="cell-input" required placeholder="Judul pengajuan" value="{{ old('subject') }}">
        </div>

        <div class="form-cell right">
            <div class="cell-label">IO / FR No. <span style="font-weight:400;color:#94a3b8;font-size:10px;">(diisi Accounting)</span></div>
            <input type="text" name="io_fr_no" class="cell-input" placeholder=" " value="{{ old('io_fr_no') }}">
        </div>

        <div class="form-cell left">
            <div class="cell-label">Nama Barang/Jasa <span class="req">*</span></div>
            <input type="text" name="nama_barang_jasa" class="cell-input" required value="{{ old('nama_barang_jasa') }}">
        </div>

        <div class="form-cell right">
            <div class="cell-label">Qty & UoM <span class="req">*</span></div>
            <div style="display:flex;gap:8px;">
                <input type="number" name="qty" class="cell-input" style="width:80px;" required min="1" value="{{ old('qty') }}" placeholder="Qty">
                <select name="uom" class="cell-input" style="flex:1;" required>
                    <option value="">UoM</option>
                    @foreach(App\Models\Ppbj::uomOptions() as $val => $label)
                    <option value="{{ $val }}" {{ old('uom') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-cell left">
            <div class="cell-label">Spesifikasi <span class="req">*</span></div>
            <textarea name="spesifikasi" class="cell-input" required rows="2">{{ old('spesifikasi') }}</textarea>
        </div>

        <div class="form-cell right">
            <div class="cell-label">Pernah Order <span class="req">*</span></div>
            <div class="radio-group">
                <label class="radio-item"><input type="radio" name="pernah_order" value="sudah" {{ old('pernah_order')=='sudah'?'checked':'' }} onchange="document.getElementById('orderBulan').style.display='block'"> Sudah</label>
                <label class="radio-item"><input type="radio" name="pernah_order" value="belum" {{ old('pernah_order','belum')=='belum'?'checked':'' }} onchange="document.getElementById('orderBulan').style.display='none'"> Belum</label>
            </div>
            <div id="orderBulan" style="margin-top:6px;{{ old('pernah_order')=='sudah'?'':'display:none;' }}">
                <input type="text" name="pernah_order_bulan" class="cell-input" placeholder="Bulan XX Tahun XX" value="{{ old('pernah_order_bulan') }}">
            </div>
        </div>

        {{-- SECTION: Background/Problem 5W1H & Risk --}}
        <div class="section-header">B. Background / Problem (5W + 1H) & Risk Analysis</div>

        {{-- (4) 5W1H Kiri --}}
        <div class="form-cell left" style="grid-row:span 6;">
            <div class="cell-label">Background / Problem (5W + 1H) <span class="req">*</span></div>
            @foreach(['what'=>'What','why'=>'Why','when'=>'When','where'=>'Where','who'=>'Who','how'=>'How'] as $key => $label)
            <div style="margin-bottom:6px;">
                <label style="font-size:11px;font-weight:600;color:#6366f1;">{{ strtolower(chr(ord('a') + $loop->index)) }}. {{ $label }}:</label>
                <textarea name="bg_{{ $key }}" class="cell-input" rows="2" required placeholder="{{ $label }}...">{{ old("bg_{$key}") }}</textarea>
            </div>
            @endforeach
        </div>

        {{-- (5) Risk Analysis Kanan --}}
        <div class="form-cell right" style="grid-row:span 6;">
            <div class="cell-label">Risk Analysis <span class="req">*</span></div>
            <textarea name="risk_analysis" class="cell-input" rows="12" required placeholder="Analisa risiko...">{{ old('risk_analysis') }}</textarea>
        </div>

        {{-- SECTION: Condition & Detail Spec --}}
        <div class="section-header">C. Condition, Specification & Urgency</div>

        {{-- (6) Condition Photo Kiri --}}
        <div class="form-cell left" style="grid-row:span 2;">
            <div class="cell-label">Condition (Sketch/Photo) <span class="req">*</span></div>
            <div class="drop-zone" id="condDrop" onclick="document.getElementById('condInput').click()">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                <span style="font-size:11px;color:#94a3b8;margin-top:4px;">Drop JPG/PNG/WEBP atau klik</span>
                <img id="condPreview" style="display:none;">
            </div>
            <input type="file" id="condInput" name="condition_photo" accept=".jpg,.jpeg,.png,.webp" style="display:none;" required>
        </div>

        {{-- (7) Detail Specification Kanan --}}
        <div class="form-cell right">
            <div class="cell-label">Detail Specification <span class="req">*</span></div>
            <div style="display:grid;gap:4px;">
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">a. Brand/Merk:</label>
                <input type="text" name="spec_brand" class="cell-input" required value="{{ old('spec_brand') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">b. Maker/Pembuat:</label>
                <input type="text" name="spec_maker" class="cell-input" required value="{{ old('spec_maker') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">c. Negara Asal:</label>
                <input type="text" name="spec_negara_asal" class="cell-input" required value="{{ old('spec_negara_asal') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">d. Lain-lain:</label>
                <textarea name="spec_lain_lain" class="cell-input" rows="1">{{ old('spec_lain_lain') }}</textarea></div>
            </div>
        </div>

        {{-- (8) Urgency Kanan --}}
        <div class="form-cell right" style="grid-row:span 2;">
            <div class="cell-label">Urgency</div>
            <div style="margin-bottom:6px;">
                <label style="font-size:11px;font-weight:600;color:#6366f1;">a. Level:</label>
                <div class="radio-group">
                    @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High'] as $val => $label)
                    <label class="radio-item"><input type="radio" name="urgency_level" value="{{ $val }}" {{ old('urgency_level')==$val?'checked':'' }} required> {{ $label }}</label>
                    @endforeach
                </div>
            </div>
            <div style="margin-bottom:6px;">
                <label style="font-size:11px;font-weight:600;color:#6366f1;">b. Potensi line stop:</label>
                <input type="text" name="potensi_line_stop" class="cell-input" placeholder="XX jam/hari" value="{{ old('potensi_line_stop') }}">
            </div>
            <div style="margin-bottom:4px;">
                <label style="font-size:11px;font-weight:600;color:#6366f1;">c-f. Pilihan (multiple):</label>
                <div class="check-group" style="flex-direction:column;">
                    <label class="check-item"><input type="checkbox" name="urgency_options[]" value="tidak_ada_backup" {{ is_array(old('urgency_options')) && in_array('tidak_ada_backup', old('urgency_options')) ? 'checked':'' }}> Tidak ada backup</label>
                    <label class="check-item"><input type="checkbox" name="urgency_options[]" value="pengadaan_baru" {{ is_array(old('urgency_options')) && in_array('pengadaan_baru', old('urgency_options')) ? 'checked':'' }}> Pengadaan baru untuk:</label>
                    <input type="text" name="pengadaan_baru_untuk" class="cell-input" placeholder="..." value="{{ old('pengadaan_baru_untuk') }}" style="margin-left:20px;">
                    <label class="check-item"><input type="checkbox" name="urgency_options[]" value="penggantian_rusak" {{ is_array(old('urgency_options')) && in_array('penggantian_rusak', old('urgency_options')) ? 'checked':'' }}> Penggantian karena: rusak</label>
                    <label class="check-item"><input type="checkbox" name="urgency_options[]" value="schedule_general_check" {{ is_array(old('urgency_options')) && in_array('schedule_general_check', old('urgency_options')) ? 'checked':'' }}> Schedule general check:</label>
                    <input type="date" name="schedule_general_check" class="cell-input" value="{{ old('schedule_general_check') }}" style="margin-left:20px;">
                </div>
            </div>
        </div>

        {{-- (9) Budget/Estimasi Kiri --}}
        <div class="form-cell left" style="display:flex; flex-direction:column; justify-content:center;">
            <div class="cell-label">Budget / Estimasi <span class="req">*</span></div>
            <div style="margin-bottom:8px;">
                <label style="font-size:11px;font-weight:600;color:#6366f1;">Tipe Budget:</label>
                <div class="radio-group">
                    @foreach(['capex'=>'Capex','foh'=>'FOH','opex'=>'OPEX','project'=>'Project'] as $val => $label)
                    <label class="radio-item"><input type="radio" name="budget_type" value="{{ $val }}" {{ old('budget_type')==$val?'checked':'' }} required> {{ $label }}</label>
                    @endforeach
                </div>
                <div id="capexUpload" style="margin-top:6px;{{ old('budget_type')=='capex'?'':'display:none;' }}">
                    <span style="font-size:10px;color:#64748b;">Lampirkan dokumen capex:</span>
                    <input type="file" name="capex_attachment" class="cell-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
            <div>
                <label style="font-size:11px;font-weight:600;color:#6366f1;">Amount (estimasi):</label>
                <div class="radio-group" style="flex-direction:column;gap:3px;">
                    @foreach(App\Models\Ppbj::budgetAmountRanges() as $val => $label)
                    <label class="radio-item"><input type="radio" name="budget_amount_range" value="{{ $val }}" {{ old('budget_amount_range')==$val?'checked':'' }} required> {{ $label }}</label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SECTION: Layout Area --}}
        <div class="section-header">D. Layout Area</div>

        {{-- (10) Layout Photo Kiri --}}
        <div class="form-cell left">
            <div class="cell-label">Layout Area Photo <span class="req">*</span></div>
            <div class="drop-zone" id="layoutDrop" onclick="document.getElementById('layoutInput').click()">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
                <span style="font-size:11px;color:#94a3b8;margin-top:4px;">Drop JPG/PNG/WEBP atau klik</span>
                <img id="layoutPreview" style="display:none;">
            </div>
            <input type="file" id="layoutInput" name="layout_photo" accept=".jpg,.jpeg,.png,.webp" style="display:none;" required>
        </div>

        {{-- (10) Lokasi Penggunaan Kanan --}}
        <div class="form-cell right">
            <div class="cell-label">Lokasi Penggunaan</div>
            <div style="display:grid;gap:4px;">
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">a. Pressline:</label>
                <input type="text" name="lokasi_pressline" class="cell-input" value="{{ old('lokasi_pressline') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">b. Sub-assy:</label>
                <input type="text" name="lokasi_sub_assy" class="cell-input" value="{{ old('lokasi_sub_assy') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">c. Metal Finish:</label>
                <input type="text" name="lokasi_metal_finish" class="cell-input" value="{{ old('lokasi_metal_finish') }}"></div>
                <div><label style="font-size:11px;font-weight:600;color:#6366f1;">d. Lain-lain:</label>
                <input type="text" name="lokasi_lain_lain" class="cell-input" value="{{ old('lokasi_lain_lain') }}"></div>
            </div>
        </div>
    </div>

    {{-- (11) Tanda Tangan / Approval --}}
    <table class="sign-table">
        <thead>
            <tr>
                <th colspan="3" style="background:#eef2ff;color:#4f46e5;">Disetujui</th>
                <th colspan="2" style="background:#f0fdf4;color:#16a34a;">Diperiksa</th>
                <th style="background:#fefce8;color:#ca8a04;">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-size:10px;color:#64748b;font-weight:600;">President Director</td>
                <td style="font-size:10px;color:#64748b;font-weight:600;">Finance Director</td>
                <td style="font-size:10px;color:#64748b;font-weight:600;">Ka. Div Finance</td>
                <td style="font-size:10px;color:#64748b;font-weight:600;">Ka. Sie Purc.</td>
                <td style="font-size:10px;color:#64748b;font-weight:600;">Ka. Dept Terkait</td>
                <td style="font-size:10px;color:#64748b;font-weight:600;">{{ $user->role_label }}</td>
            </tr>
            <tr>
                <td class="sign-box"></td>
                <td class="sign-box"></td>
                <td class="sign-box"></td>
                <td class="sign-box"></td>
                <td class="sign-box"></td>
                <td class="sign-box"></td>
            </tr>
            <tr>
                <td style="font-size:10px;color:#334155;">—</td>
                <td style="font-size:10px;color:#334155;">—</td>
                <td style="font-size:10px;color:#334155;">—</td>
                <td style="font-size:10px;color:#334155;">Bramansyah B.I.</td>
                <td style="font-size:10px;color:#334155;">—</td>
                <td style="font-size:10px;color:#334155;">{{ $user->name }}</td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top:6px;font-size:10px;color:#94a3b8;">
        <strong>*</strong> Wajib diisi &nbsp;|&nbsp;
        <strong>***</strong> ≤ 50jt: Kadiv &nbsp;|&nbsp; 50jt-100jt: s/d Direktur &nbsp;|&nbsp; > 100jt: s/d Presdir
    </div>

    {{-- Signature Pad --}}
    <div class="no-print" style="margin-top:24px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; padding:16px;">
        <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Tanda Tangan Digital Pengaju <span style="color:#ef4444">*</span></label>
        <div style="position:relative; touch-action:none; background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
            <canvas id="signaturePad" width="460" height="160" style="width:100%; max-width:460px; height:160px; cursor:crosshair;"></canvas>
            <button type="button" onclick="clearSignature()" style="position:absolute; top:8px; right:8px; font-size:10px; padding:4px 8px; border-radius:4px; border:none; background:#f1f5f9; color:#475569; cursor:pointer;">Hapus</button>
        </div>
        <p style="font-size:10px; color:#64748b; margin-top:4px;">Gambarkan tanda tangan Anda menggunakan mouse atau sentuhan.</p>
        <input type="hidden" name="signature_data" id="signatureInput">
    </div>

    {{-- Submit --}}
    <div class="no-print" style="display:flex;justify-content:center;gap:12px;margin-top:24px;">
        <a href="{{ route('pengajuan.index') }}" class="btn-print" style="text-decoration:none; background:#fff; display:inline-flex; align-items:center;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <button type="submit" class="btn-submit">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
            Ajukan PPBJ
        </button>
    </div>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePad');
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let lastX = 0, lastY = 0;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        if (e.touches && e.touches.length > 0) {
            return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
        }
        return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
    }

    function startDraw(e) { 
        e.preventDefault();
        drawing = true; 
        const pos = getPos(e); 
        lastX = pos.x; lastY = pos.y; 
    }
    function draw(e) { 
        if (!drawing) return; 
        e.preventDefault();
        const pos = getPos(e);
        ctx.beginPath(); 
        ctx.moveTo(lastX, lastY); 
        ctx.lineTo(pos.x, pos.y);
        ctx.strokeStyle = '#1e293b'; 
        ctx.lineWidth = 2.5; 
        ctx.lineCap = 'round'; 
        ctx.lineJoin = 'round';
        ctx.stroke();
        lastX = pos.x; lastY = pos.y;
    }
    function stopDraw() { drawing = false; }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    window.clearSignature = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    };

    function isCanvasBlank() {
        const blankCanvas = document.createElement('canvas');
        blankCanvas.width = canvas.width;
        blankCanvas.height = canvas.height;
        return canvas.toDataURL() === blankCanvas.toDataURL();
    }

    document.getElementById('ppbjForm').addEventListener('submit', function(e) {
        if (isCanvasBlank()) {
            e.preventDefault();
            alert('Harap bubuhkan tanda tangan digital Anda terlebih dahulu sebelum mengajukan PPBJ.');
            return false;
        }
        document.getElementById('signatureInput').value = canvas.toDataURL('image/png');
    });
});
</script>
@endsection

@push('scripts')
<script>
// File preview for drop zones
function setupDropZone(dropId, inputId, previewId) {
    const drop = document.getElementById(dropId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    ['dragenter','dragover'].forEach(e => {
        drop.addEventListener(e, ev => { ev.preventDefault(); drop.classList.add('dragover'); });
    });
    ['dragleave','drop'].forEach(e => {
        drop.addEventListener(e, ev => { ev.preventDefault(); drop.classList.remove('dragover'); });
    });
    drop.addEventListener('drop', ev => {
        input.files = ev.dataTransfer.files;
        showPreview(input.files[0], preview);
    });
    input.addEventListener('change', () => {
        if(input.files[0]) showPreview(input.files[0], preview);
    });
}
function showPreview(file, img) {
    if(!file) return;
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
    reader.readAsDataURL(file);
}
setupDropZone('condDrop','condInput','condPreview');
setupDropZone('layoutDrop','layoutInput','layoutPreview');

// Capex upload toggle
document.querySelectorAll('input[name="budget_type"]').forEach(r => {
    r.addEventListener('change', () => {
        document.getElementById('capexUpload').style.display = r.value === 'capex' ? 'block' : 'none';
    });
});

function fillDummyData() {
    // text/number/textarea
    const fills = {
        'subject': 'Pengadaan Mesin Kompresor Industri',
        'ia_no': 'IA-2026/001',
        'io_fr_no': 'IO-2026-991',
        'nama_barang_jasa': 'Kompresor Angin 5HP',
        'qty': '2',
        'spesifikasi': 'Tekanan 10 bar, 3 phase, garansi 2 tahun.',
        'pernah_order_bulan': 'Januari 2025',
        'bg_what': 'Kompresor utama di line 1 rusak',
        'bg_why': 'Usia mesin sudah lebih dari 10 tahun dan sering bocor',
        'bg_when': 'Mulai bermasalah sejak bulan lalu',
        'bg_where': 'Area produksi Pressline 1',
        'bg_who': 'Operator line dan tim maintenance',
        'bg_how': 'Perlu diganti segera agar produksi tidak terganggu',
        'risk_analysis': 'Jika tidak diganti, line 1 akan sering mati dan output produksi turun 20%.',
        'spec_brand': 'Hitachi',
        'spec_maker': 'Hitachi',
        'spec_negara_asal': 'Jepang',
        'spec_lain_lain': 'Dilengkapi dengan auto-drain valve.',
        'potensi_line_stop': 'Ya, 2 jam per hari',
        'lokasi_pressline': 'Line 1A',
        'lokasi_sub_assy': '-',
        'lokasi_metal_finish': '-',
        'lokasi_lain_lain': 'Ruang kompresor utama'
    };

    for(let k in fills) {
        let el = document.querySelector(`[name="${k}"]`);
        if(el) el.value = fills[k];
    }

    // selects
    let dept = document.querySelector('[name="department_section"]');
    if(dept && dept.options.length > 1) dept.selectedIndex = 1;
    
    let uom = document.querySelector('[name="uom"]');
    if(uom) uom.value = 'unit';

    // radios
    let clickRadio = (name, val) => {
        let el = document.querySelector(`[name="${name}"][value="${val}"]`);
        if(el) { el.checked = true; el.dispatchEvent(new Event('change')); }
    };
    clickRadio('pernah_order', 'sudah');
    clickRadio('urgency_level', 'high');
    clickRadio('budget_type', 'expense');
    clickRadio('budget_amount_range', '10m_to_50m');

    // checkboxes
    let check = document.querySelector('[name="urgency_options[]"][value="penggantian_rusak"]');
    if(check) check.checked = true;
    
    document.getElementById('orderBulan').style.display = 'block';

    alert("Data testing berhasil di-generate! (Silakan lampirkan gambar Condition Photo secara manual)");
}
</script>
@endpush