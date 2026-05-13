<?php
// dump_security_checklist_head.php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

$usefulData = [];
foreach ($data as $i => $row) {
    if ($i > 50) break; // Limit to first 50 rows
    // Filter out empty rows
    if (!empty(array_filter($row))) {
        $usefulData[$i] = $row;
    }
}

echo json_encode($usefulData, JSON_PRETTY_PRINT);
