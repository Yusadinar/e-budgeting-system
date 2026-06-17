<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mapping = [
    'Quality' => 'Quality Management System',
    'Quality Assurance' => 'Quality Management System',
    'PPLC' => 'Production Planning & Logistic Control',
    'GA' => 'Human Capital & General Services',
    'Human Resource' => 'Human Capital & General Services',
    'HRGA' => 'Human Capital & General Services',
    'Purchasing' => 'Procurement',
    'FIN & ACC' => 'Finance Accounting',
];

$ccs = \App\Models\CostCenter::whereNull('dept_id')->get();
$mappedCount = 0;
foreach($ccs as $cc) {
    if (isset($mapping[$cc->department_group])) {
        $dept = \App\Models\Department::where('dept_name', $mapping[$cc->department_group])->first();
        if ($dept) {
            $cc->dept_id = $dept->id;
            $cc->save();
            $mappedCount++;
        }
    }
}
echo "Mapped " . $mappedCount . " cost centers.\n";
