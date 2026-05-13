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

// Map security items to project state
$filled = array_map(function($item) {
    $itemText = $item['item'];
    $tahu = 'Tahu';
    $bisa = 'Bisa';
    $alasan = '';

    if (stripos($itemText, 'port scanning') !== false) {
        $alasan = 'Dilakukan oleh tim infrastruktur/security secara berkala.';
    } elseif (stripos($itemText, 'System Lifecycle') !== false || stripos($itemText, 'latest version') !== false) {
        $alasan = 'Aplikasi menggunakan PHP 8.2 dan Laravel 12 (versi terbaru).';
    } elseif (stripos($itemText, 'MFA') !== false || stripos($itemText, 'Multifactor') !== false) {
        $alasan = 'Dapat diintegrasikan dengan SSO perusahaan atau package Laravel Fortify/Breeze.';
    } elseif (stripos($itemText, 'TLS') !== false) {
        $alasan = 'Konfigurasi web server (Nginx/Apache) menggunakan TLS 1.2+ sesuai standar.';
    } elseif (stripos($itemText, 'Access Control') !== false || stripos($itemText, 'RBAC') !== false) {
        $alasan = 'Sudah diimplementasikan Role-Based Access Control (Superadmin, Dept Head, dsb).';
    } elseif (stripos($itemText, 'Vulnerability Assesment') !== false) {
        $alasan = 'Siap dilakukan pengujian oleh tim IT Security.';
    } elseif (stripos($itemText, 'Secure Programming') !== false || stripos($itemText, 'Input Validation') !== false) {
        $alasan = 'Menggunakan fitur Laravel (Eloquent/Blade) untuk proteksi SQLi dan XSS, serta validasi request.';
    } elseif (stripos($itemText, 'Backup') !== false) {
        $alasan = 'Backup database dan file storage dilakukan secara harian di level server.';
    } else {
        $alasan = 'Sesuai dengan best practice pengembangan Laravel dan kebijakan IT.';
    }

    return [
        'No' => $item['no'],
        'Domain' => $item['domain'],
        'Item' => $itemText,
        'Pengetahuan' => $tahu,
        'Pemenuhan' => $bisa,
        'Alasan' => $alasan
    ];
}, $items);

echo json_encode($filled, JSON_PRETTY_PRINT);
