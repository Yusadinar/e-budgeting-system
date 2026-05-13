<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

$row21 = $data[21] ?? [];
for ($i = 0; $i < 40; $i++) {
    $val = $row21[$i] ?? '[NULL]';
    echo "Col $i: $val\n";
}
