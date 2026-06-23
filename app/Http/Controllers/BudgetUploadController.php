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
    // KONSTANTA BULAN
    // =========================================================
    private const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    // =========================================================
    // HALAMAN UPLOAD
    // =========================================================

    public function index(): View
    {
        abort_if(! Auth::user()->isKaDept(), 403, 'Hanya Kepala Departemen yang bisa mengakses fitur ini.');

        $user     = Auth::user();
        $dept     = $user->department;
        $uploads  = BudgetUpload::where('dept_id', $dept->id)
                        ->orderByDesc('created_at')
                        ->take(10)
                        ->get();

        $costCenters = \App\Models\CostCenter::where('dept_id', $dept->id)
                        ->with('currentBudget')
                        ->orderBy('plant')
                        ->orderBy('expense_type')
                        ->orderBy('cost_center_name')
                        ->get();

        $deptBudget = $dept->currentBudget;

        return view('budget.upload', compact('dept', 'uploads', 'costCenters', 'deptBudget'));
    }

    // =========================================================
    // OL SCHEDULE & COLUMN DEFINITION
    // =========================================================

    /**
     * Definisi jadwal OL berdasarkan tahun fiskal.
     */
    public static function getOlSchedules(int $fy): array
    {
        return [
            [
                'ol'       => 'OL1',
                'periode'  => "Mei {$fy} – Jul {$fy}",
                'due'      => "{$fy}-05-10",
                'due_label'=> "10 Mei {$fy}",
                'type'     => 'regular',
            ],
            [
                'ol'       => 'OL2',
                'periode'  => "Agus {$fy} – Okt {$fy}",
                'due'      => "{$fy}-08-10",
                'due_label'=> "10 Agustus {$fy}",
                'type'     => 'regular',
            ],
            [
                'ol'       => 'OL2 ADJ',
                'periode'  => "Sept {$fy} – Des {$fy}",
                'due'      => "{$fy}-10-10",
                'due_label'=> "10 Oktober {$fy}",
                'type'     => 'adjustment',
            ],
            [
                'ol'       => 'OL3',
                'periode'  => "Nov {$fy} – Apr " . ($fy + 1),
                'due'      => "{$fy}-11-10",
                'due_label'=> "10 November {$fy}",
                'type'     => 'regular',
            ],
        ];
    }

    /**
     * Tentukan OL berikutnya yang perlu di-upload berdasarkan tanggal.
     */
    public static function detectNextOl(int $fy): string
    {
        $today = now();
        foreach (self::getOlSchedules($fy) as $s) {
            if ($today->lt(\Carbon\Carbon::parse($s['due']))) {
                return $s['ol'];
            }
        }
        return 'OL1'; // fallback
    }

    /**
     * Generate definisi kolom budget berdasarkan OL.
     * Setiap kolom: ['key' => 'mei_2026', 'label' => 'Mei 2026', 'type' => 'monthly'|'ytd']
     */
    public static function getOlColumns(string $ol, int $fy): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $cols = [];

        switch (strtoupper(str_replace(' ', '', $ol))) {
            case 'OL1':
                // Mei-Des {fy} monthly, {fy+1} YTD, {fy+2} YTD
                for ($m = 4; $m <= 11; $m++) {
                    $cols[] = ['key' => strtolower($months[$m]) . "_{$fy}", 'label' => "{$months[$m]} {$fy}", 'type' => 'monthly', 'year' => $fy, 'month' => $months[$m]];
                }
                break;

            case 'OL2':
                // Agu-Des {fy} monthly, {fy+1} YTD, {fy+2} YTD
                for ($m = 7; $m <= 11; $m++) {
                    $cols[] = ['key' => strtolower($months[$m]) . "_{$fy}", 'label' => "{$months[$m]} {$fy}", 'type' => 'monthly', 'year' => $fy, 'month' => $months[$m]];
                }
                break;

            case 'OL3':
                // Nov-Des {fy} monthly, {fy+1} YTD, {fy+2} YTD
                for ($m = 10; $m <= 11; $m++) {
                    $cols[] = ['key' => strtolower($months[$m]) . "_{$fy}", 'label' => "{$months[$m]} {$fy}", 'type' => 'monthly', 'year' => $fy, 'month' => $months[$m]];
                }
                break;

            case 'OL2ADJ':
                // Sep-Des {fy} monthly, {fy+1} YTD, {fy+2} YTD
                for ($m = 8; $m <= 11; $m++) {
                    $cols[] = ['key' => strtolower($months[$m]) . "_{$fy}", 'label' => "{$months[$m]} {$fy}", 'type' => 'monthly', 'year' => $fy, 'month' => $months[$m]];
                }
                break;

            default:
                // Fallback ke OL1
                for ($m = 4; $m <= 11; $m++) {
                    $cols[] = ['key' => strtolower($months[$m]) . "_{$fy}", 'label' => "{$months[$m]} {$fy}", 'type' => 'monthly', 'year' => $fy, 'month' => $months[$m]];
                }
                break;
        }

        // 2. Add full 12 Months for Next Year (2027)
        for ($m = 0; $m <= 11; $m++) {
            $cols[] = ['key' => strtolower($months[$m]) . "_" . ($fy + 1), 'label' => "{$months[$m]} " . ($fy + 1), 'type' => 'monthly', 'year' => $fy + 1, 'month' => $months[$m]];
        }

        // 3. Add YTD for the following 3 Years (2028, 2029, 2030)
        $cols[] = ['key' => "ytd_" . ($fy + 2), 'label' => ($fy + 2) . " YTD", 'type' => 'ytd', 'year' => $fy + 2, 'month' => 'YTD'];
        $cols[] = ['key' => "ytd_" . ($fy + 3), 'label' => ($fy + 3) . " YTD", 'type' => 'ytd', 'year' => $fy + 3, 'month' => 'YTD'];
        $cols[] = ['key' => "ytd_" . ($fy + 4), 'label' => ($fy + 4) . " YTD", 'type' => 'ytd', 'year' => $fy + 4, 'month' => 'YTD'];

        return $cols;
    }

    // =========================================================
    // DOWNLOAD TEMPLATE DINAMIS
    // =========================================================

    public function downloadTemplate(Request $request)
    {
        abort_if(! Auth::user()->isKaDept(), 403);

        $user = Auth::user();
        $dept = $user->department;
        $type = strtoupper($request->query('type', 'FOH'));
        $fy   = now()->year;

        // Tentukan OL berdasarkan tanggal saat ini
        $olNumber = self::detectNextOl($fy);
        $olClean  = strtoupper(str_replace(' ', '', $olNumber));
        $budgetCols = self::getOlColumns($olNumber, $fy);

        // Ambil cost center per departemen & tipe
        $costCenters = \App\Models\CostCenter::where('dept_id', $dept->id)
            ->where('expense_type', $type)
            ->orderBy('plant')
            ->orderBy('cost_center_name')
            ->get();

        if ($costCenters->isEmpty()) {
            $costCenters = \App\Models\CostCenter::where('dept_id', $dept->id)
                ->orderBy('plant')
                ->orderBy('expense_type')
                ->orderBy('cost_center_name')
                ->get();
        }

        $writer = new \App\Services\XlsxWriter();
        $writer->setSheetName("Lampiran Budget {$type}");

        // Kolom tetap: NO, DESC, CC, AJU, PREV = 5 kolom
        // Kolom budget: dinamis
        $totalCols = 5 + count($budgetCols);

        // Column widths
        $widths = [5, 40, 15, 15, 15]; // NO, DESC, CC, AJU, PREV
        foreach ($budgetCols as $col) {
            $widths[] = $col['type'] === 'ytd' ? 15 : 13;
        }
        $writer->setColumnWidths($widths);

        $pad = array_fill(0, $totalCols, '');

        $indexToCol = function ($index) {
            $col = '';
            $index++;
            while ($index > 0) {
                $index--;
                $col = chr(65 + ($index % 26)) . $col;
                $index = intdiv($index, 26);
            }
            return $col;
        };

        // Row 1: Company Header
        $writer->addRow(array_merge(['PT. INTI PANTJA PRESS INDUSTRI'], array_fill(0, $totalCols - 1, '')), 'header');
        $writer->addMerge("A1:" . $indexToCol($totalCols - 1) . "1");

        // Row 2: Department
        $writer->addRow(array_merge(["Dept: {$dept->dept_name} — Lampiran Budget {$type}"], array_fill(0, $totalCols - 1, '')), 'subheader');
        $writer->addMerge("A2:" . $indexToCol($totalCols - 1) . "2");

        // Row 3: OL Info
        $writer->addRow(array_merge(["Pengisian Budget {$olNumber} | FY {$fy}"], array_fill(0, $totalCols - 1, '')), 'subheader');
        $writer->addMerge("A3:" . $indexToCol($totalCols - 1) . "3");

        // Row 4: Empty separator
        $writer->addRow(array_fill(0, $totalCols, ''));

        // Row 5: Column Headers (Top Level: Fixed Headers & Year/YTD)
        $fixedHeaders = ['NO', 'DESCRIPTION', 'COST CENTER', 'AJU/IA', 'PREVENTIVE'];
        $headersRow5 = $fixedHeaders;
        $headersRow6 = ['', '', '', '', ''];

        // Merge fixed headers vertically
        for ($i = 0; $i < 5; $i++) {
            $colLetter = $indexToCol($i);
            $writer->addMerge("{$colLetter}5:{$colLetter}6");
        }

        $colIdx = 5;
        $currentYear = null;
        $yearStartCol = null;

        foreach ($budgetCols as $col) {
            if ($col['type'] === 'ytd') {
                // If there's an active year merge pending for monthly columns, close it
                if ($currentYear !== null) {
                    $startLetter = $indexToCol($yearStartCol);
                    $endLetter = $indexToCol($colIdx - 1);
                    if ($startLetter !== $endLetter) {
                        $writer->addMerge("{$startLetter}5:{$endLetter}5");
                    }
                    $currentYear = null;
                }

                $headersRow5[] = $col['year'] . " YTD";
                $headersRow6[] = ''; // Merge vertically
                $colLetter = $indexToCol($colIdx);
                $writer->addMerge("{$colLetter}5:{$colLetter}6");
            } else {
                // Monthly columns: group by year
                if ($currentYear !== $col['year']) {
                    // Close previous year merge
                    if ($currentYear !== null) {
                        $startLetter = $indexToCol($yearStartCol);
                        $endLetter = $indexToCol($colIdx - 1);
                        if ($startLetter !== $endLetter) {
                            $writer->addMerge("{$startLetter}5:{$endLetter}5");
                        }
                    }
                    $currentYear = $col['year'];
                    $yearStartCol = $colIdx;
                }
                $headersRow5[] = $col['year']; // The year will be printed in every cell, but merging will hide the duplicates or center them
                $headersRow6[] = $col['month'];
            }
            $colIdx++;
        }

        // Close any pending year merge at the end
        if ($currentYear !== null) {
            $startLetter = $indexToCol($yearStartCol);
            $endLetter = $indexToCol($colIdx - 1);
            if ($startLetter !== $endLetter) {
                $writer->addMerge("{$startLetter}5:{$endLetter}5");
            }
        }

        $writer->addRow($headersRow5, 'header');
        $writer->addRow($headersRow6, 'subheader');

        $isDummy = $request->query('dummy') == '1';

        // Data rows
        $currentPlant = null;
        $rowNo = 1;

        foreach ($costCenters as $cc) {
            if ($cc->plant !== $currentPlant) {
                $currentPlant = $cc->plant;
                $plantLabel = match ($cc->plant) {
                    'IBEK' => 'PLANT BEKASI (IBEK)',
                    'IKAR' => 'PLANT KARAWANG (IKAR)',
                    'HO'   => 'HEAD OFFICE (HO)',
                    default => $cc->plant,
                };
                $row = ['', $plantLabel, '', '', ''];
                foreach ($budgetCols as $_) $row[] = '';
                $writer->addRow($row, 'highlight');
            }

            $desc = $cc->cost_center_name ?? '';
            if ($isDummy) {
                $desc = $desc ? "Pengadaan {$desc} (Dummy)" : "Item Dummy";
            }
            $aju  = $isDummy ? 'AJU' : '';
            $prev = $isDummy ? 'NO' : '';

            $dataRow = [$rowNo, $desc, $cc->cost_center_code ?? 'N/A', $aju, $prev];
            foreach ($budgetCols as $col) {
                $dataRow[] = $isDummy ? rand(1, 8) * 1000000 : 0;
            }
            $writer->addRow($dataRow, 'data');
            $rowNo++;
        }

        // TOTAL row
        $totalRow = ['', 'TOTAL', '', '', ''];
        foreach ($budgetCols as $_) $totalRow[] = '';
        $writer->addRow($totalRow, 'subheader');

        $suffix = $isDummy ? '_DUMMY' : '';
        $filename = "Template_Budget_{$type}_{$dept->dept_name}_{$fy}_{$olClean}{$suffix}.xlsx";

        return $writer->download($filename);
    }

    // =========================================================
    // PARSE EXCEL → PREVIEW
    // =========================================================

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
            $rawRows  = $parser->parse(0);

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
            'budget_columns' => ['required', 'string'], // JSON encoded column definitions

            'items'                => ['required', 'array', 'min:1'],
            'items.*.no'           => ['nullable', 'string', 'max:20'],
            'items.*.description'  => ['required', 'string', 'max:500'],
            'items.*.cost_center'  => ['nullable', 'string', 'max:50'],
            'items.*.aju_ia'       => ['nullable', 'string', 'max:50'],
            'items.*.preventive'   => ['nullable', 'string', 'max:100'],
            'items.*.amounts'      => ['nullable', 'array'],
        ]);

        $user = Auth::user();
        $dept = $user->department;

        $budgetColumns = json_decode($validated['budget_columns'], true) ?: [];

        // Filter & map items
        $items = collect($validated['items'])
            ->filter(fn($item) => ! empty(trim($item['description'] ?? '')))
            ->values()
            ->map(function ($item) use ($budgetColumns) {
                $mapped = [
                    'no'          => $item['no'] ?? '',
                    'description' => $item['description'],
                    'cost_center' => $item['cost_center'] ?? '',
                    'aju_ia'      => $item['aju_ia'] ?? '',
                    'preventive'  => $item['preventive'] ?? '',
                    'amounts'     => [],
                ];
                // Map setiap kolom budget
                foreach ($budgetColumns as $idx => $col) {
                    $key = $col['key'];
                    $mapped['amounts'][$key] = (float) ($item['amounts'][$idx] ?? 0);
                }
                return $mapped;
            })
            ->toArray();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Minimal ada 1 item budget yang valid.'])->withInput();
        }

        // Total = sum semua kolom budget per item
        $totalAmount = collect($items)->sum(fn($item) => array_sum($item['amounts'] ?? []));

        DB::transaction(function () use ($validated, $items, $totalAmount, $dept, $user, $budgetColumns) {
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

            // Sinkronisasi ke AnnualBudget per Cost Center
            $budgetByCC = [];
            $deptGlobalPlan = 0;

            foreach ($items as $item) {
                $ccCode = $item['cost_center'];
                $amount = array_sum($item['amounts'] ?? []);

                if (empty($ccCode) || $ccCode === 'N/A') {
                    $deptGlobalPlan += $amount;
                } else {
                    $budgetByCC[$ccCode] = ($budgetByCC[$ccCode] ?? 0) + $amount;
                }
            }

            // Update Global Dept Budget
            if ($deptGlobalPlan > 0 || empty($budgetByCC)) {
                $annualDept = \App\Models\AnnualBudget::firstOrCreate(
                    ['dept_id' => $dept->id, 'cost_center_id' => null, 'fiscal_year' => $validated['fiscal_year']],
                    ['total_used' => 0, 'total_reserved' => 0, 'total_plan' => 0]
                );
                $annualDept->total_plan += $deptGlobalPlan;
                $annualDept->save();
            }

            // Update per Cost Center
            foreach ($budgetByCC as $ccCode => $plan) {
                $cc = \App\Models\CostCenter::where('cost_center_code', $ccCode)->first();
                if ($cc) {
                    $annualCc = \App\Models\AnnualBudget::firstOrCreate(
                        ['cost_center_id' => $cc->id, 'fiscal_year' => $validated['fiscal_year']],
                        ['dept_id' => $cc->dept_id ?? $dept->id, 'total_used' => 0, 'total_reserved' => 0, 'total_plan' => 0]
                    );
                    $annualCc->dept_id = $cc->dept_id ?? $dept->id;
                    $annualCc->total_plan += $plan;
                    $annualCc->save();
                }
            }
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

    private function extractTemplateData(array $rawRows, string $fileName, $dept): array
    {
        // ── Cari OL Number ────────────────────────────────────────
        $olNumber = null;
        $olPeriod = null;
        $fiscalYear = now()->year;

        foreach ($rawRows as $row) {
            $rowText = implode(' ', array_filter($row, fn($v) => $v !== null && $v !== ''));

            if (preg_match('/OL\s*(\d+(?:\s*ADJ)?)/i', $rowText, $m)) {
                $olNumber = 'OL' . strtoupper(str_replace(' ', '', $m[1]));
            }
            if (preg_match('/FY\s*(\d{4})/i', $rowText, $m)) {
                $fiscalYear = (int) $m[1];
            }
            if (preg_match('/Pengisian Budget\s+(OL\d+[^|]*)\|\s*FY\s*(\d{4})/i', $rowText, $m)) {
                $olNumber = trim($m[1]);
                $fiscalYear = (int) $m[2];
                $olPeriod = "Pengisian Budget {$olNumber} | FY {$fiscalYear}";
            }
        }

        if (!$olNumber) {
            $olNumber = self::detectNextOl($fiscalYear);
        }

        // ── Cari kategori ─────────────────────────────────────────
        $category = 'OPEX';
        foreach ($rawRows as $row) {
            $rowText = strtoupper(implode(' ', array_filter($row, fn($v) => $v !== null && $v !== '')));
            if (str_contains($rowText, 'CAPEX')) { $category = 'CAPEX'; break; }
            if (str_contains($rowText, 'FOH'))   { $category = 'FOH';   break; }
        }

        // ── Definisi kolom budget berdasarkan OL ──────────────────
        $budgetCols = self::getOlColumns($olNumber, $fiscalYear);

        // ── Cari baris header data ────────────────────────────────
        $dataStartRow = null;
        $headerRowIdx = null;
        foreach ($rawRows as $rowIdx => $row) {
            $rowText = strtoupper(implode(' ', array_filter($row, fn($v) => $v !== null && $v !== '')));
            if (str_contains($rowText, 'DESCRIPTION') && str_contains($rowText, 'NO')) {
                $headerRowIdx = $rowIdx;
                $dataStartRow = $rowIdx + 1;
                // Skip sub-header rows
                while (isset($rawRows[$dataStartRow])) {
                    $sub = strtoupper(implode(' ', array_filter($rawRows[$dataStartRow], fn($v) => $v !== null)));
                    if (preg_match('/MONTHLY|YTD|ACTUAL|RP/', $sub)) {
                        $dataStartRow++;
                    } else {
                        break;
                    }
                }
                break;
            }
        }

        // ── Map kolom budget mulai dari kolom index 5 ─────────────
        $items = [];
        $budgetColStart = 5; // kolom 0-4 = NO, DESC, CC, AJU, PREV

        if ($dataStartRow !== null) {
            $rowNo = 1;
            for ($r = $dataStartRow; $r < count($rawRows); $r++) {
                $row = $rawRows[$r] ?? [];
                $get = fn(int $col) => isset($row[$col]) && $row[$col] !== '' ? $row[$col] : null;

                $description = $get(1);
                if ($description === null || trim($description) === '') {
                    continue;
                }

                $upperDesc = strtoupper(trim($description));
                if (
                    in_array($upperDesc, ['TOTAL', 'SUB TOTAL', 'GRAND TOTAL', 'JUMLAH']) ||
                    str_starts_with($upperDesc, 'PLANT') ||
                    str_starts_with($upperDesc, 'HEAD OFFICE')
                ) {
                    continue;
                }

                $amounts = [];
                foreach ($budgetCols as $colIdx => $col) {
                    $amounts[$col['key']] = $this->parseNum($get($budgetColStart + $colIdx));
                }

                $items[] = [
                    'no'          => $get(0) ?? $rowNo,
                    'description' => trim($description),
                    'cost_center' => $get(2),
                    'aju_ia'      => $get(3),
                    'preventive'  => $get(4),
                    'amounts'     => $amounts,
                ];
                $rowNo++;
            }
        }

        // Scaffold jika kosong
        if (empty($items)) {
            $emptyAmounts = [];
            foreach ($budgetCols as $col) {
                $emptyAmounts[$col['key']] = 0;
            }
            for ($i = 1; $i <= 3; $i++) {
                $items[] = [
                    'no' => $i, 'description' => '', 'cost_center' => '',
                    'aju_ia' => '', 'preventive' => '', 'amounts' => $emptyAmounts,
                ];
            }
        }

        $totalAmount = collect($items)->sum(fn($item) => array_sum($item['amounts'] ?? []));

        return [
            'outlook_number' => $olNumber,
            'outlook_period' => $olPeriod,
            'category'       => $category,
            'fiscal_year'    => $fiscalYear,
            'file_name'      => $fileName,
            'budget_columns' => $budgetCols,
            'items'          => $items,
            'total_amount'   => $totalAmount,
        ];
    }

    /**
     * Convert string to float.
     */
    private function parseNum(mixed $value): float
    {
        if ($value === null || $value === '') return 0.0;
        $clean = str_replace(['.', ',', ' '], ['', '.', ''], (string) $value);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }
}
