<?php
// app/Http/Controllers/BudgetUploadController.php

namespace App\Http\Controllers;

use App\Models\BudgetUpload;
use App\Services\XlsxParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetUploadController extends Controller
{
    // =========================================================
    // HALAMAN UPLOAD
    // =========================================================

    /**
     * Halaman form upload Excel.
     */
    public function index(): View
    {
        abort_if(! Auth::user()->isKaDept(), 403, 'Hanya Kepala Departemen yang bisa mengakses fitur ini.');

        $user     = Auth::user();
        $dept     = $user->department;
        $uploads  = BudgetUpload::where('dept_id', $dept->id)
                        ->orderByDesc('created_at')
                        ->take(10)
                        ->get();

        return view('budget.upload', compact('dept', 'uploads'));
    }

    // =========================================================
    // PARSE EXCEL → PREVIEW
    // =========================================================

    /**
     * Terima file Excel, parse, tampilkan preview untuk review.
     */
    public function parse(Request $request): View|RedirectResponse
    {
        abort_if(! Auth::user()->isKaDept(), 403);

        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'excel_file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'excel_file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'excel_file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = Auth::user();
        $dept = $user->department;

        try {
            $file     = $request->file('excel_file');
            $filePath = $file->getRealPath();
            $fileName = $file->getClientOriginalName();

            $parser   = new XlsxParser($filePath);
            $rawRows  = $parser->parse(0); // Sheet pertama

            // ── Ekstrak info header dari template ─────────────────────
            $parsed = $this->extractTemplateData($rawRows, $fileName, $dept);

        } catch (\Throwable $e) {
            return back()
                ->withErrors(['excel_file' => 'Gagal membaca file Excel: ' . $e->getMessage()])
                ->withInput();
        }

        return view('budget.upload-preview', compact('parsed', 'dept'));
    }

    // =========================================================
    // SIMPAN SETELAH REVIEW
    // =========================================================

    /**
     * Simpan data yang sudah di-review ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isKaDept(), 403);

        $validated = $request->validate([
            'outlook_number' => ['required', 'string', 'max:20'],
            'outlook_period' => ['nullable', 'string', 'max:100'],
            'category'       => ['required', 'in:CAPEX,FOH,OPEX'],
            'fiscal_year'    => ['required', 'integer', 'min:2020', 'max:2099'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'file_name'      => ['nullable', 'string', 'max:255'],

            // Items dari form (diedit user)
            'items'                => ['required', 'array', 'min:1'],
            'items.*.no'           => ['nullable', 'string', 'max:20'],
            'items.*.description'  => ['required', 'string', 'max:500'],
            'items.*.cost_center'  => ['nullable', 'string', 'max:50'],
            'items.*.aju_ia'       => ['nullable', 'string', 'max:50'],
            'items.*.preventive'   => ['nullable', 'string', 'max:100'],
            'items.*.amount_2025'  => ['nullable', 'numeric', 'min:0'],
            'items.*.amount_2026'  => ['nullable', 'numeric', 'min:0'],
            'items.*.amount_2027'  => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_jan'   => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_feb'   => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_mar'   => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_apr'   => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_mei'   => ['nullable', 'numeric', 'min:0'],
            'items.*.actual_total' => ['nullable', 'numeric', 'min:0'],
            'items.*.saldo'        => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();
        $dept = $user->department;

        // Filter out empty rows
        $items = collect($validated['items'])
            ->filter(fn($item) => ! empty(trim($item['description'] ?? '')))
            ->values()
            ->map(function ($item) {
                return [
                    'no'           => $item['no']           ?? '',
                    'description'  => $item['description'],
                    'cost_center'  => $item['cost_center']  ?? '',
                    'aju_ia'       => $item['aju_ia']       ?? '',
                    'preventive'   => $item['preventive']   ?? '',
                    'amount_2025'  => (float) ($item['amount_2025']  ?? 0),
                    'amount_2026'  => (float) ($item['amount_2026']  ?? 0),
                    'amount_2027'  => (float) ($item['amount_2027']  ?? 0),
                    'actual_jan'   => (float) ($item['actual_jan']   ?? 0),
                    'actual_feb'   => (float) ($item['actual_feb']   ?? 0),
                    'actual_mar'   => (float) ($item['actual_mar']   ?? 0),
                    'actual_apr'   => (float) ($item['actual_apr']   ?? 0),
                    'actual_mei'   => (float) ($item['actual_mei']   ?? 0),
                    'actual_total' => (float) ($item['actual_total'] ?? 0),
                    'saldo'        => (float) ($item['saldo']        ?? 0),
                ];
            })
            ->toArray();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Minimal ada 1 item budget yang valid.'])->withInput();
        }

        $totalAmount = collect($items)->sum('amount_2026');

        DB::transaction(function () use ($validated, $items, $totalAmount, $dept, $user) {
            BudgetUpload::create([
                'dept_id'        => $dept->id,
                'uploaded_by'    => $user->id,
                'file_name'      => $validated['file_name'] ?? null,
                'outlook_number' => $validated['outlook_number'],
                'outlook_period' => $validated['outlook_period'] ?? null,
                'category'       => $validated['category'],
                'fiscal_year'    => $validated['fiscal_year'],
                'items'          => $items,
                'total_amount'   => $totalAmount,
                'notes'          => $validated['notes'] ?? null,
                'status'         => 'confirmed',
            ]);
        });

        return redirect()->route('budget.upload.index')
            ->with('success', 'Budget berhasil disimpan dari file Excel. Total ' . count($items) . ' item tercatat.');
    }

    // =========================================================
    // DETAIL UPLOAD
    // =========================================================

    public function show(BudgetUpload $budgetUpload): View
    {
        abort_if(! Auth::user()->isKaDept(), 403);
        abort_if($budgetUpload->dept_id !== Auth::user()->department?->id, 403);

        return view('budget.upload-show', compact('budgetUpload'));
    }

    // =========================================================
    // PRIVATE HELPER — Extract template data from raw rows
    // =========================================================

    /**
     * Baca informasi dari template Excel Lampiran Budget.
     * Template layout (0-indexed rows):
     *   Row 0  : Header perusahaan
     *   Row 1  : Nama dept / divisi
     *   Row 3  : "Budget XXXX s/d XXXX"  (periode)
     *   Row 4  : Kolom OL (OL 1, OL 2, OL 3, ...)
     *   Row 5  : "CAPEX / FOH ATAU OPEX"
     *   Row 9+ : Data item (NO, DESCRIPTION, COST CENTER, AJU/IA, PREVENTIVE, amounts…)
     */
    private function extractTemplateData(array $rawRows, string $fileName, $dept): array
    {
        // ── Cari OL Number ────────────────────────────────────────
        $olNumber = 'OL1';
        $olPeriod = null;

        foreach ($rawRows as $rowIdx => $row) {
            $rowText = implode(' ', array_filter($row, fn($v) => $v !== null && $v !== ''));

            // Cari pattern OL diikuti angka (misal "OL 3" atau "OL3")
            if (preg_match('/OL\s*(\d+(?:\s*ADJ)?)/i', $rowText, $m)) {
                $olNumber = 'OL' . strtoupper(str_replace(' ', '', $m[1]));
                break;
            }
        }

        // ── Cari periode outlook ───────────────────────────────────
        foreach ($rawRows as $rowIdx => $row) {
            $rowText = implode(' ', array_filter($row, fn($v) => $v !== null && $v !== ''));
            if (preg_match('/Budget\s+(\d{4})\s+s\/d\s+(\d{4})/i', $rowText, $m)) {
                $olPeriod = "Budget {$m[1]} s/d {$m[2]}";
                break;
            }
        }

        // ── Cari kategori ─────────────────────────────────────────
        $category = 'OPEX'; // default
        foreach ($rawRows as $rowIdx => $row) {
            $rowText = strtoupper(implode(' ', array_filter($row, fn($v) => $v !== null && $v !== '')));
            if (str_contains($rowText, 'CAPEX')) { $category = 'CAPEX'; break; }
            if (str_contains($rowText, 'FOH'))   { $category = 'FOH';   break; }
        }

        // ── Cari tahun fiskal ──────────────────────────────────────
        $fiscalYear = now()->year;
        foreach ($rawRows as $row) {
            $rowText = implode(' ', array_filter($row, fn($v) => $v !== null && $v !== ''));
            if (preg_match('/20(\d{2})/', $rowText, $m)) {
                $fiscalYear = (int) ('20' . $m[1]);
                break;
            }
        }

        // ── Ekstrak baris data item ────────────────────────────────
        // Header baris data biasanya mengandung "NO", "DESCRIPTION", "COST CENTER"
        $dataStartRow = null;
        foreach ($rawRows as $rowIdx => $row) {
            $rowText = strtoupper(implode(' ', array_filter($row, fn($v) => $v !== null && $v !== '')));
            if (str_contains($rowText, 'DESCRIPTION') && str_contains($rowText, 'NO')) {
                $dataStartRow = $rowIdx + 1; // baris data mulai setelah header
                // Skip jika masih ada sub-header
                while (isset($rawRows[$dataStartRow])) {
                    $sub = strtoupper(implode(' ', array_filter($rawRows[$dataStartRow], fn($v) => $v !== null)));
                    if (preg_match('/MAR|JAN|FEB|APR|MEI|TOTAL|SALDO/', $sub)) {
                        $dataStartRow++;
                    } else {
                        break;
                    }
                }
                break;
            }
        }

        // ── Mapping kolom berdasarkan template ────────────────────
        // Template kolom (0-indexed):
        // 0=NO, 1=DESCRIPTION, 2=COST CENTER, 3=AJU/IA, 4=PREVENTIVE,
        // 5=MAR2025, 6=TOTAL2025, 7=TOTAL2026, 8=TOTAL2027,
        // 9=JAN, 10=FEB, 11=MAR, 12=APR, 13=MEI, 14=TOTAL_ACTUAL, 15=SALDO

        $items = [];

        if ($dataStartRow !== null) {
            $rowNo = 1;
            for ($r = $dataStartRow; $r < count($rawRows); $r++) {
                $row = $rawRows[$r] ?? [];

                // Ambil nilai per kolom (dengan fallback ke null)
                $get = fn(int $col) => isset($row[$col]) && $row[$col] !== '' ? $row[$col] : null;

                $description = $get(1);
                if ($description === null || trim($description) === '') {
                    continue; // skip baris kosong
                }

                // Skip baris total / sub-total
                $upperDesc = strtoupper(trim($description));
                if (in_array($upperDesc, ['TOTAL', 'SUB TOTAL', 'GRAND TOTAL', 'JUMLAH'])) {
                    continue;
                }

                $items[] = [
                    'no'           => $get(0) ?? $rowNo,
                    'description'  => trim($description),
                    'cost_center'  => $get(2),
                    'aju_ia'       => $get(3),
                    'preventive'   => $get(4),
                    'amount_2025'  => $this->parseNum($get(5) ?? $get(6)),
                    'amount_2026'  => $this->parseNum($get(7)),
                    'amount_2027'  => $this->parseNum($get(8)),
                    'actual_jan'   => $this->parseNum($get(9)),
                    'actual_feb'   => $this->parseNum($get(10)),
                    'actual_mar'   => $this->parseNum($get(11)),
                    'actual_apr'   => $this->parseNum($get(12)),
                    'actual_mei'   => $this->parseNum($get(13)),
                    'actual_total' => $this->parseNum($get(14)),
                    'saldo'        => $this->parseNum($get(15)),
                ];
                $rowNo++;
            }
        }

        // Jika tidak ada item terdeteksi, buat 3 baris kosong sebagai scaffold
        if (empty($items)) {
            for ($i = 1; $i <= 3; $i++) {
                $items[] = [
                    'no'           => $i,
                    'description'  => '',
                    'cost_center'  => '',
                    'aju_ia'       => '',
                    'preventive'   => '',
                    'amount_2025'  => 0,
                    'amount_2026'  => 0,
                    'amount_2027'  => 0,
                    'actual_jan'   => 0,
                    'actual_feb'   => 0,
                    'actual_mar'   => 0,
                    'actual_apr'   => 0,
                    'actual_mei'   => 0,
                    'actual_total' => 0,
                    'saldo'        => 0,
                ];
            }
        }

        return [
            'outlook_number' => $olNumber,
            'outlook_period' => $olPeriod,
            'category'       => $category,
            'fiscal_year'    => $fiscalYear,
            'file_name'      => $fileName,
            'items'          => $items,
            'total_amount'   => collect($items)->sum('amount_2026'),
        ];
    }

    /**
     * Convert string to float (handles Indonesian format with periods as thousand separators).
     */
    private function parseNum(mixed $value): float
    {
        if ($value === null || $value === '') return 0.0;
        // Remove thousand separators (dots in Indonesian format)
        $clean = str_replace(['.', ',', ' '], ['', '.', ''], (string) $value);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }
}
