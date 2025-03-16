<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ✅ Tambahkan ini

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gunakan Auth::check() dan Auth::user() untuk menghindari error
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized'); // Jika bukan admin, tampilkan error 403
    }
}
