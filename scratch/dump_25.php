<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

for ($i = 0; $i < 25; $i++) {
    if (isset($data[$i])) {
        echo "Row $i: " . json_encode($data[$i]) . "\n";
    }
}
