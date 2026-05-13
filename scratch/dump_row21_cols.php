<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

$row21 = $data[21] ?? [];
ksort($row21);
foreach ($row21 as $col => $val) {
    echo "Col $col: $val\n";
}
