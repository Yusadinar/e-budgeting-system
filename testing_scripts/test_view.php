<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/superadmin/users/edit', 'GET');
$request->setLaravelSession($app['session']->driver('array'));
app()->instance('request', $request);

$user = App\Models\User::where('name', 'like', '%yusa%')->first();
$controller = new App\Http\Controllers\SuperAdmin\UserController();
$view = $controller->edit($user);
$html = $view->with('errors', new \Illuminate\Support\MessageBag())->with('user', $user)->render();
preg_match('/x-data=\'(.*?)\'/s', $html, $matches);
if (isset($matches[1])) {
    echo "FOUND X-DATA:\n" . $matches[1] . "\n";
} else {
    echo "X-DATA NOT FOUND\n";
}
