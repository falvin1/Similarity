<?php

namespace App\Http;

use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'api' => [
            \App\Http\Middleware\HandleCors::class, 
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, 
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
        ],
        
    ];
    protected $middlewareAliases =[
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}
