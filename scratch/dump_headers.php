<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

echo "Row 20: " . implode(" | ", array_map(fn($v) => $v ?? '', $data[20] ?? [])) . "\n";
echo "Row 21: " . implode(" | ", array_map(fn($v) => $v ?? '', $data[21] ?? [])) . "\n";
echo "Row 22: " . implode(" | ", array_map(fn($v) => $v ?? '', $data[22] ?? [])) . "\n";
