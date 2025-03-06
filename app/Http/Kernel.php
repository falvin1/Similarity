<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'api' => [
            \App\Http\Middleware\HandleCors::class, // Tambahkan middleware CORS
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // KOMENTAR INI DULU
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
    
    
}
