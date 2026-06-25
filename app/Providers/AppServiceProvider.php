<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::directive('format_rupiah', function ($expression) {
            return "<?php 
                \$val = (float)($expression);
                if (abs(\$val) >= 1000000000) {
                    \$res = number_format(\$val / 1000000000, 2, ',', '.');
                    if (str_ends_with(\$res, ',00')) { \$res = substr(\$res, 0, -3); }
                    elseif (str_ends_with(\$res, '0')) { \$res = substr(\$res, 0, -1); }
                    echo 'Rp ' . \$res . ' Miliar';
                } elseif (abs(\$val) >= 1000000) {
                    \$res = number_format(\$val / 1000000, 1, ',', '.');
                    if (str_ends_with(\$res, ',0')) { \$res = substr(\$res, 0, -2); }
                    echo 'Rp ' . \$res . ' Juta';
                } else {
                    echo 'Rp ' . number_format(\$val, 0, ',', '.');
                }
            ?>";
        });
    }
}
