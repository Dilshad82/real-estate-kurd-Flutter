<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOfficeApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
      public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // إذا كان المستخدم مسجلاً كـ مكتب (office) ولم يتم الموافقة عليه بعد (is_approved == false)
        if ($user && $user->role === 'office' && !$user->is_approved) {
            
            // استثناء مسارات صفحة الانتظار وتسجيل الخروج لتجنب التكرار اللانهائي (Redirect Loop)
            if (!$request->is('office/pending') && !$request->is('logout*')) {
                return redirect()->route('office.pending');
            }
        }

        return $next($request);
    }
}
