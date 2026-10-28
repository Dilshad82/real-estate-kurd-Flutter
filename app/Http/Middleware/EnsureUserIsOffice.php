<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| وسيط التحقق من صلاحيات المكاتب (EnsureUserIsOffice Middleware)
|--------------------------------------------------------------------------
| الوظيفة: حماية مسارات لوحة تحكم "مكتبي العقاري" (/office/*) والتأكد 
| من أن المستخدم يملك دوراً ينتمي للمكاتب العقارية أو إدارة النظام.
|
*/

class EnsureUserIsOffice
{
    /**
     * معالجة الطلب القادم وتدقيق صلاحية دور المستخدم (Role Verification)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التحقق من تسجيل دخول المستخدم أولاً في الجلسة الممررة
        if (auth()->check()) {
            
            $user = auth()->user();

            // 2. تنظيف نص الدور (Role): إزالة المسافات الزائدة وتحويل الأحرف إلى صغيرة (lowercase)
            // لتفادي أخطاء حالة الأحرف (Case Sensitivity) مع دعم الأحرف العربية والكردية (mb_strtolower)
            $userRole = mb_strtolower(trim($user->role ?? ''));

            // 3. التحقق من قيمة حقل is_admin سواء كانت Boolean أو String أو Integer (1 / true)
            $isAdminField = filter_var($user->is_admin ?? false, FILTER_VALIDATE_BOOLEAN);

            // 4. فحص الصلاحيات المسموح لها بإدارة صفحة "مكتبي العقاري":
            // - 'office': صاحب مكتب عقاري عادي.
            // - $isAdminField: إذا كان الحساب يملك قيمة true أو 1 في حقل الأدمن.
            // - يحتوي على 'admin' أو 'ئەدمین' أو 'ادمن' أو 'ادمان': كافة صيغ الأدمن العام وأدمن المحافظات (مثل admin_duhok).
            if (
                $userRole === 'office' || 
                $isAdminField ||
                str_contains($userRole, 'admin') || 
                str_contains($userRole, 'ادمن') || 
                str_contains($userRole, 'أدمن') || 
                str_contains($userRole, 'ادمان') ||
                str_contains($userRole, 'ئەدمین')
            ) {
                return $next($request); // إكمال الطلب والسماح للمستخدم بفتح الصفحة
            }
        }

        // 5. في حال لم تنطبق الشروط أعلاه (مثلاً: مستخدم عادي 'user' أو زائر غير مسجل)، يتم إلغاء الطلب بإظهار خطأ 403
        abort(403, 'غير مسموح لك بالوصول لصفحة المكاتب العقارية.');
    }
}