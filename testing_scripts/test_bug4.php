<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$phs = \App\Models\ProposalHarga::whereIn('ph_number', ['001/PURCH/IPPI/VI/2026', '002/PURCH/IPPI/VI/2026'])->with('ppbj')->get();
echo json_encode($phs, JSON_PRETTY_PRINT);
