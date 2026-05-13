<?php
// list_sheets.php
$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$zip = new ZipArchive();
if ($zip->open($filePath) !== true) {
    die("Failed to open $filePath");
}

$workbookXml = $zip->getFromName('xl/workbook.xml');
$xml = new SimpleXMLElement($workbookXml);
$sheets = [];
foreach ($xml->sheets->sheet as $sheet) {
    $sheets[] = [
        'name' => (string) $sheet['name'],
        'sheetId' => (string) $sheet['sheetId'],
        'rId' => (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id']
    ];
}

echo json_encode($sheets, JSON_PRETTY_PRINT);
$zip->close();
