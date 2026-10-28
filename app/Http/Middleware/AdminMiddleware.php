<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = mb_strtolower(trim($user->role ?? ''));
        
        // فحص شامل لجميع حالات الأدمن
        $isAdmin = !empty($user->is_admin) 
            || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) 
            || str_starts_with($userRole, 'admin_');

        if ($isAdmin) {
            return $next($request);
        }

        abort(403, 'غير مسموح لك بالوصول لصفحة المكاتب العقارية.');
    }
}