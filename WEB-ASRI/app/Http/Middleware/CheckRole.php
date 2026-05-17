<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Cek apakah role user sesuai dengan parameter di route
        // Contoh di route: ->middleware('role:admin')
        if (strtolower($user->role) !== strtolower($role)) {
    abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');

        }

        return $next($request);
    }
}