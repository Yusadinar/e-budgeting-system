<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ccs = \App\Models\CostCenter::all();
foreach($ccs as $cc) {
    $dept = \App\Models\Department::where('dept_name', $cc->department_group)->first();
    if($dept) {
        $cc->dept_id = $dept->id;
        $cc->save();
    }
}
echo "Populated dept_id for " . \App\Models\CostCenter::whereNotNull('dept_id')->count() . " cost centers.\n";
