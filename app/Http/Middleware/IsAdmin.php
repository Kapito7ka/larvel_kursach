<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Неавторизований доступ'], 401);
        }

        if (Auth::user()->status !== 'admin') {
            return response()->json(['message' => 'Доступ заборонено. Потрібні права адміністратора.'], 403);
        }

        return $next($request);
    }
}
