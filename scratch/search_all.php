<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

foreach ($data as $i => $row) {
    foreach ($row as $col => $val) {
        if (stripos($val, 'Pengetahuan') !== false || stripos($val, 'Pemenuhan') !== false) {
            echo "Found at Row $i, Col $col: $val\n";
        }
    }
}
