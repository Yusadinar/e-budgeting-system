<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'like', '%yusa%')->first();
$controller = new App\Http\Controllers\SuperAdmin\UserController();
$view = $controller->edit($user);
$sections = $view->getData()['sectionsByDept'];
echo "SECTIONS JSON IS:\n";
echo json_encode($sections);
echo "\n";
