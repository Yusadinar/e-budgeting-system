<?php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1);

for ($i = 10; $i <= 22; $i++) {
    if (isset($data[$i])) {
        echo "Row $i:\n";
        foreach ($data[$i] as $col => $val) {
            if ($val !== null && $val !== '') {
                echo "  Col $col: $val\n";
            }
        }
    }
}
