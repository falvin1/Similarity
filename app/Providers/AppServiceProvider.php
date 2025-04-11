<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

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
        //
        Route::middleware('api')
    ->prefix('api')
    ->group(base_path('routes/api.php'));
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('google-drive-credentials.json'));  

    Http::globalOptions([
        'timeout' => 1000000,
    ]);
    }
    
}
