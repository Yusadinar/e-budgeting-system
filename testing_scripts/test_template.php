<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = new \App\Http\Controllers\BudgetUploadController;
$req = request();
$req->merge(['type'=>'FOH', 'dummy'=>'1']);

// Call downloadTemplate, it returns a BinaryFileResponse
$res = $c->downloadTemplate($req, 14); // 14 = IKAR

// The file is written to a tmp file before downloading.
// Let's modify the controller temporarily to save it to public/test.xlsx
// Or we can just grab the tmp file from the response.
$file = $res->getFile()->getPathname();

// Now parse it
$parser = new \App\Services\XlsxParser($file);
$rows = $parser->parse();

// Print row 5 and 6
echo "ROW 5: " . json_encode($rows[4]) . "\n";
echo "ROW 6: " . json_encode($rows[5]) . "\n";
