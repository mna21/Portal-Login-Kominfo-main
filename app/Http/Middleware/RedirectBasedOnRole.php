<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{

    public function handle($request, Closure $next)
    {
        // Jika user adalah admin atau superadmin, lanjutkan
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            return $next($request);
        }

        // Jika bukan admin, hanya bisa akses data miliknya
        if ($request->route('dawis') !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
    /*
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        return $next($request);
    }
    */
}
