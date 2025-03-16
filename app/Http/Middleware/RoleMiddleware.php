<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        // If user is not logged in, redirect to login
        if (!$user) {
            return redirect('/login');
        }
        
        // Check if the user has the required role
        if ($user->role !== $role) {
            // If user is admin but trying to access regular dashboard
            if ($user->role === 'admin' && $request->path() === 'dashboard') {
                return redirect('/admin/dashboard');
            }
            
            // If user is regular but trying to access admin dashboard
            if ($user->role !== 'admin' && str_starts_with($request->path(), 'admin')) {
                return redirect('/dashboard');
            }
            
            // Default redirect for unauthorized access
            return redirect('/dashboard');
        }
        
        return $next($request);
    }
}