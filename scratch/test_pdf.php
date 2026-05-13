<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Ppbj;

try {
    $ppbj = Ppbj::first();
    if (!$ppbj) {
        echo "No PPBJ found to test.\n";
        exit;
    }
    
    echo "Generating PDF for PPBJ: " . $ppbj->ppbj_number . "\n";
    
    // Simulasikan Request jika diperlukan oleh view (seperti asset())
    // Namun public_path() harusnya aman
    
    $pdf = Pdf::loadView('pengajuan.pdf-ppbj', compact('ppbj'))
              ->setPaper('a4', 'portrait')
              ->setWarnings(false);
              
    $output = $pdf->output();
    file_put_contents(__DIR__.'/../test_ppbj.pdf', $output);
    
    echo "PDF generated successfully: test_ppbj.pdf\n";
} catch (\Exception $e) {
    echo "Error generating PDF: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
