<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckGeneralAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // السماح فقط للمدير العام الرئيسي بدخول هذه الصفحة
        if ($user && ($user->role === 'admin' || $user->is_admin == 1)) {
            return $next($request);
        }

        // إذا كان أدمن محافظة وحاول دخول صفحة المكاتب، يتم تحويله فوراً لصفحة المناطق مع رسالة تنبيه
        return redirect()->route('admin.lookups.index')->with('error', 'ليس لديك صلاحية للدخول إلى صفحة إدارة المكاتب العقارية.');
    }
}