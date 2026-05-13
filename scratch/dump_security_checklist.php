<?php
// dump_security_checklist.php
require 'vendor/autoload.php';
require 'app/Services/XlsxParser.php';

use App\Services\XlsxParser;

$filePath = '2026 Release Mgt Flow Form ASMO3.xlsx';
$parser = new XlsxParser($filePath);
$data = $parser->parse(1); // Index 1 is "Security Checklist Form"

echo json_encode($data, JSON_PRETTY_PRINT);
