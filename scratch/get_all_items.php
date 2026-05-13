<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

$items = [];
foreach ($data as $i => $row) {
    if ($i < 23) continue;
    $no = $row[1] ?? '';
    $domain = $row[2] ?? '';
    $itemText = $row[4] ?? '';
    
    if (empty($no) && empty($itemText)) continue;
    
    $items[] = [
        'no' => $no,
        'domain' => $domain,
        'item' => $itemText
    ];
}

echo json_encode($items, JSON_PRETTY_PRINT);
