<?php
// app/Services/XlsxParser.php

namespace App\Services;

use ZipArchive;
use SimpleXMLElement;
use RuntimeException;

/**
 * Native PHP XLSX parser — no external dependencies.
 * Reads an .xlsx file and returns raw cell data using ZipArchive + SimpleXML.
 */
class XlsxParser
{
    private string $filePath;
    private array $sharedStrings = [];

    public function __construct(string $filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }
        $this->filePath = $filePath;
    }

    /**
     * Parse the first sheet and return a 2D array of rows => cells.
     *
     * @return array<int, array<int, string|null>>
     */
    public function parse(int $sheetIndex = 0): array
    {
        $zip = new ZipArchive();
        if ($zip->open($this->filePath) !== true) {
            throw new RuntimeException('Cannot open XLSX file. Make sure it is a valid .xlsx format.');
        }

        // 1. Load shared strings
        $this->sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $ss = new SimpleXMLElement($ssXml);
            foreach ($ss->si as $si) {
                // Concatenate all <t> nodes (handles rich-text)
                $value = '';
                foreach ($si->r as $r) {
                    $value .= (string) $r->t;
                }
                if ($value === '') {
                    $value = (string) $si->t;
                }
                $this->sharedStrings[] = $value;
            }
        }

        // 2. Find sheet list
        $workbookRelsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $sheetFiles = [];
        if ($workbookRelsXml !== false) {
            $rels = new SimpleXMLElement($workbookRelsXml);
            foreach ($rels->Relationship as $rel) {
                $type = (string) $rel['Type'];
                if (str_ends_with($type, '/worksheet')) {
                    $sheetFiles[] = 'xl/' . ltrim((string) $rel['Target'], '/');
                }
            }
        }

        if (empty($sheetFiles)) {
            // Fallback: try sheet1 directly
            $sheetFiles = ['xl/worksheets/sheet1.xml'];
        }

        // 3. Read requested sheet
        $targetSheet = $sheetFiles[$sheetIndex] ?? $sheetFiles[0];
        $sheetXml = $zip->getFromName($targetSheet);
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException("Sheet not found in XLSX.");
        }

        return $this->parseSheet($sheetXml);
    }

    private function parseSheet(string $sheetXml): array
    {
        $xml = new SimpleXMLElement($sheetXml);
        $xml->registerXPathNamespace('ns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];

        foreach ($xml->sheetData->row as $row) {
            $rowIndex = (int) $row['r'] - 1; // 0-based
            $rowData  = [];

            foreach ($row->c as $cell) {
                $colIndex = $this->colToIndex((string) $cell['r']);
                $type     = (string) $cell['t'];

                if ($type === 's') {
                    // Shared string
                    $idx            = (int) $cell->v;
                    $rowData[$colIndex] = $this->sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $rowData[$colIndex] = (string) $cell->is->t;
                } elseif ($type === 'b') {
                    $rowData[$colIndex] = (string) $cell->v === '1' ? 'TRUE' : 'FALSE';
                } else {
                    // Number or formula result
                    $rowData[$colIndex] = isset($cell->v) ? (string) $cell->v : null;
                }
            }

            if (! empty($rowData)) {
                $rows[$rowIndex] = $rowData;
            }
        }

        // Normalize to dense 2D array
        if (empty($rows)) {
            return [];
        }

        $maxRow = max(array_keys($rows));
        $result = [];
        for ($r = 0; $r <= $maxRow; $r++) {
            $result[$r] = $rows[$r] ?? [];
        }

        return $result;
    }

    /**
     * Convert cell reference (e.g. "A1", "AB3") to zero-based column index.
     */
    private function colToIndex(string $cellRef): int
    {
        // Extract only the column letters
        preg_match('/([A-Z]+)/', strtoupper($cellRef), $m);
        $col   = $m[1] ?? 'A';
        $index = 0;
        $len   = strlen($col);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }
}
