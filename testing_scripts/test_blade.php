<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/superadmin/users/edit', 'GET');
$request->setLaravelSession($app['session']->driver('array'));
app()->instance('request', $request);

$user = App\Models\User::where('name', 'like', '%yusa%')->first();
$sectionsByDept = App\Models\User::where('role', 'ka_sie')
    ->whereNotNull('section')
    ->whereNotNull('dept_id')
    ->get()
    ->groupBy('dept_id')
    ->map(fn($users) => $users->pluck('section')->unique()->values()->all());

$html = <<<BLADE
        <form method="POST" action="xxx" class="space-y-5"
              x-data='{ 
                  role: @json(old("role", \$user->role)), 
                  dept: @json((string) old("dept_id", \$user->dept_id)),
                  oldSection: @json(old("section", \$user->section)),
                  sectionsData: @json(\$sectionsByDept ?? []),
                  availableSections() {
                      return this.dept && this.sectionsData[this.dept] ? this.sectionsData[this.dept] : [];
                  }
              }'>
BLADE;

$compiled = Illuminate\Support\Facades\Blade::compileString($html);
// We need to evaluate it with variables
ob_start();
extract(['user' => $user, 'sectionsByDept' => $sectionsByDept]);
eval('?>' . $compiled);
$rendered = ob_get_clean();

echo "RENDERED HTML:\n$rendered\n";
