<?php
// app/Services/XlsxWriter.php

namespace App\Services;

use ZipArchive;

/**
 * Native PHP XLSX writer — no external dependencies.
 * Creates a simple .xlsx file with styled headers and data rows.
 */
class XlsxWriter
{
    private array $rows = [];
    private array $columnWidths = [];
    private array $merges = [];
    private array $headerStyles = [];
    private string $sheetName = 'Sheet1';

    /**
     * Set sheet name.
     */
    public function setSheetName(string $name): self
    {
        $this->sheetName = $name;
        return $this;
    }

    /**
     * Add a row of data.
     * @param array $cells Array of cell values
     * @param string|null $style 'header'|'subheader'|'data'|null
     */
    public function addRow(array $cells, ?string $style = null): self
    {
        $this->rows[] = ['cells' => $cells, 'style' => $style];
        return $this;
    }

    /**
     * Set column widths (0-indexed).
     */
    public function setColumnWidths(array $widths): self
    {
        $this->columnWidths = $widths;
        return $this;
    }

    /**
     * Add a cell merge. e.g. 'A1:D1'
     */
    public function addMerge(string $range): self
    {
        $this->merges[] = $range;
        return $this;
    }

    /**
     * Generate the .xlsx file and return path.
     */
    public function save(string $filePath): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        // Build components
        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rels());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRels());
        $zip->addFromString('xl/workbook.xml', $this->workbook());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/sharedStrings.xml', $this->sharedStrings());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet());

        $zip->close();
        return true;
    }

    /**
     * Generate and stream as download response.
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $this->save($tmpFile);

        return response()->download($tmpFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ])->deleteFileAfterSend(true);
    }

    // =============================================
    // XML Generators
    // =============================================

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>';
    }

    private function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }

    private function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    private function workbook(): string
    {
        $name = htmlspecialchars($this->sheetName, ENT_XML1);
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="' . $name . '" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
    }

    /**
     * Styles:
     * 0 = default
     * 1 = header (bold, bg indigo, white text, centered)
     * 2 = subheader (bold, bg light gray, centered)
     * 3 = data (normal, borders)
     * 4 = cost-center highlight (bg light yellow, bordered)
     */
    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <numFmts count="1">
        <numFmt numFmtId="164" formatCode="#,##0"/>
    </numFmts>
    <fonts count="3">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><name val="Calibri"/></font>
    </fonts>
    <fills count="5">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FF4338CA"/></patternFill></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border/>
        <border>
            <left style="thin"><color auto="1"/></left>
            <right style="thin"><color auto="1"/></right>
            <top style="thin"><color auto="1"/></top>
            <bottom style="thin"><color auto="1"/></bottom>
        </border>
    </borders>
    <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
    <cellXfs count="5">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <xf numFmtId="0" fontId="2" fillId="3" borderId="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/>
        <xf numFmtId="0" fontId="2" fillId="4" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>
    </cellXfs>
</styleSheet>';
    }

    /**
     * Build shared strings from all rows.
     */
    private function sharedStrings(): string
    {
        $strings = [];
        $index = 0;

        foreach ($this->rows as $row) {
            foreach ($row['cells'] as $cell) {
                if ($cell !== null && $cell !== '' && !is_numeric($cell)) {
                    $val = (string) $cell;
                    if (!isset($strings[$val])) {
                        $strings[$val] = $index++;
                    }
                }
            }
        }

        $count = count($strings);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $count . '" uniqueCount="' . $count . '">';

        foreach ($strings as $str => $_idx) {
            $xml .= '<si><t>' . htmlspecialchars((string) $str, ENT_XML1) . '</t></si>';
        }

        $xml .= '</sst>';

        $this->_sharedStringMap = $strings;
        return $xml;
    }

    private array $_sharedStringMap = [];

    /**
     * Build the sheet XML.
     */
    private function sheet(): string
    {
        // Make sure shared strings are built
        if (empty($this->_sharedStringMap)) {
            $this->sharedStrings();
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        // Column widths
        if (!empty($this->columnWidths)) {
            $xml .= '<cols>';
            foreach ($this->columnWidths as $colIdx => $width) {
                $col = $colIdx + 1;
                $xml .= '<col min="' . $col . '" max="' . $col . '" width="' . $width . '" customWidth="1"/>';
            }
            $xml .= '</cols>';
        }

        $xml .= '<sheetData>';

        foreach ($this->rows as $rowIdx => $row) {
            $rowNum = $rowIdx + 1;
            $styleId = match ($row['style']) {
                'header'    => 1,
                'subheader' => 2,
                'data'      => 3,
                'highlight' => 4,
                default     => 0,
            };

            $xml .= '<row r="' . $rowNum . '">';

            foreach ($row['cells'] as $colIdx => $value) {
                $colLetter = $this->indexToCol($colIdx);
                $ref = $colLetter . $rowNum;

                if ($value === null || $value === '') {
                    $xml .= '<c r="' . $ref . '" s="' . $styleId . '"/>';
                } elseif (is_numeric($value)) {
                    $xml .= '<c r="' . $ref . '" s="' . $styleId . '"><v>' . $value . '</v></c>';
                } else {
                    // Shared string
                    $ssIdx = $this->_sharedStringMap[(string) $value] ?? 0;
                    $xml .= '<c r="' . $ref . '" s="' . $styleId . '" t="s"><v>' . $ssIdx . '</v></c>';
                }
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData>';

        // Merges
        if (!empty($this->merges)) {
            $xml .= '<mergeCells count="' . count($this->merges) . '">';
            foreach ($this->merges as $range) {
                $xml .= '<mergeCell ref="' . $range . '"/>';
            }
            $xml .= '</mergeCells>';
        }

        $xml .= '</worksheet>';
        return $xml;
    }

    /**
     * Convert 0-based column index to Excel column letter (A, B, ..., Z, AA, AB, ...).
     */
    private function indexToCol(int $index): string
    {
        $col = '';
        $index++;
        while ($index > 0) {
            $index--;
            $col = chr(65 + ($index % 26)) . $col;
            $index = intdiv($index, 26);
        }
        return $col;
    }
}
