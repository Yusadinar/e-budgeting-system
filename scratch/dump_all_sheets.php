<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);

for ($s = 0; $s < 3; $s++) {
    echo "Sheet $s:\n";
    $data = $parser->parse($s);
    for ($i = 0; $i < 40; $i++) {
        if (isset($data[$i]) && !empty(array_filter($data[$i]))) {
            echo "Row $i: " . implode(" | ", array_map(fn($v) => (string)$v, $data[$i])) . "\n";
        }
    }
}
