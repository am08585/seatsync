<?php

namespace App\Filament\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class AdminAccessMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        // Check if user is authenticated and is admin
        if (!$user || !$user->is_admin) {
            // Redirect non-admin users away from admin panel
            return redirect()->route('login')
                ->with('error', 'You must be an administrator to access this area.');
        }

        return $next($request);
    }
}
