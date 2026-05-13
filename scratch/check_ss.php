<?php
$zip = new ZipArchive();
if ($zip->open('2026 Release Mgt Flow Form ASMO3.xlsx') === true) {
    $ss = $zip->getFromName('xl/sharedStrings.xml');
    if (stripos($ss, 'Pengetahuan') !== false) {
        echo "Found Pengetahuan in sharedStrings\n";
    }
    if (stripos($ss, 'Pemenuhan') !== false) {
        echo "Found Pemenuhan in sharedStrings\n";
    }
    $zip->close();
}
