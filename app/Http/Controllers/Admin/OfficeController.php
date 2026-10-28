<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| متحكم إدارة المكاتب العقارية والأدمنية (OfficeController)
|--------------------------------------------------------------------------
| الوظائف الأساسية:
| 1. عرض طلبات تسجيل المكاتب العقارية وتصفيتها بحسب المحافظة والصلاحيات.
| 2. قبول وتفعيل حسابات المكاتب العقارية (is_approved = true).
| 3. تعليق أو رفض حسابات المكاتب العقارية (is_approved = false).
| 4. تعيين وتعيين أدمنية المحافظات وإدارة الصلاحيات المزدوجة.
|
*/

class OfficeController extends Controller
{
    /**
     * عرض قائمة المكاتب العقارية وأدمن المحافظات مع الفلترة الذكية.
     */
    /**
     * عرض قائمة المكاتب العقارية وأدمن المحافظات مع الفلترة الذكية.
     */
    /**
     * عرض قائمة المكاتب العقارية وأدمن المحافظات مع الفلترة الذكية.
     */
    /**
     * عرض قائمة المكاتب العقارية الحقيقية التي تمتلك إجازة مكتب فقط
     */
    public function index()
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        // 1. التحقق الشامل من صلاحية الأدمن
        $isAdmin = !empty($currentUser->is_admin) 
            || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) 
            || str_starts_with($userRole, 'admin_');

        if (!$isAdmin) {
            abort(403, 'غير مسموح لك بالوصول لصفحة المكاتب العقارية.');
        }

        // 2. الفلترة الصارمة: جلب الحسابات التي لديها ملف إجازة مكتب عقاري فقط وتجاهل باقي الحسابات
        $officesQuery = User::whereNotNull('office_license')
            ->where('office_license', '!=', '')
            ->where(function ($q) {
                $q->where('role', 'office')
                  ->orWhere('role', 'like', 'admin_%');
            })
            ->with(['city', 'allowedCities']);

        // 3. تطبيق الفلترة حسب صلاحية أدمن المحافظة
        if (str_starts_with($userRole, 'admin_')) {
            $cityRole = str_replace('admin_', '', $userRole);

            if (is_numeric($cityRole)) {
                $officesQuery->where('city_id', $cityRole);
            } else {
                if ($userRole === 'admin_duhok') {
                    $officesQuery->whereHas('city', function ($q) {
                        $q->where('name', 'like', '%دهوك%')->orWhere('name', 'like', '%duhok%')->orWhere('name', 'like', '%دهۆک%');
                    });
                } elseif ($userRole === 'admin_erbil') {
                    $officesQuery->whereHas('city', function ($q) {
                        $q->where('name', 'like', '%هولێر%')->orWhere('name', 'like', '%erbil%')->orWhere('name', 'like', '%أربيل%');
                    });
                } elseif ($userRole === 'admin_sulaymaniyah') {
                    $officesQuery->whereHas('city', function ($q) {
                        $q->where('name', 'like', '%سلێمانی%')->orWhere('name', 'like', '%sulaymaniyah%')->orWhere('name', 'like', '%سليمانية%');
                    });
                } else {
                    if ($currentUser->city_id) {
                        $officesQuery->where('city_id', $currentUser->city_id);
                    }
                }
            }
        }

        // جلب النتائج مع الترقيم
        $offices = $officesQuery->latest()->paginate(10);

        // جلب قائمة أدمنية المحافظات
        $cityAdmins = User::where('role', 'like', '%admin%')->get();

        return view('admin.offices.index', compact('offices', 'cityAdmins'));
    }

    /**
     * قبول وتفعيل حساب المكتب العقاري (تخطي صفحة الانتظار)
     */
    public function approve(User $user)
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        $isAdmin = !empty($currentUser->is_admin) 
            || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) 
            || str_starts_with($userRole, 'admin_');

        if (!$isAdmin) {
            abort(403, 'غير مصرح لك بهذه العملية.');
        }

        $user->update([
            'is_approved' => true
        ]);

        $msg = app()->getLocale() == 'ku'
            ? 'مۆڵەتا نوسینگێ ب سەرکەفتیانە هاتە پەسەندکرن!'
            : 'تمت الموافقة على حساب المكتب وتفعيله بنجاح.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * رفض أو إيقاف حساب المكتب العقاري (إعادته لصفحة الانتظار)
     */
    public function reject(User $user)
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        $isAdmin = !empty($currentUser->is_admin) 
            || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) 
            || str_starts_with($userRole, 'admin_');

        if (!$isAdmin) {
            abort(403, 'غير مصرح لك بهذه العملية.');
        }

        $user->update([
            'is_approved' => false
        ]);

        $msg = app()->getLocale() == 'ku'
            ? 'مۆڵەتا نوسینگێ هاتە راگرتن.'
            : 'تم إلغاء تفعيل حساب المكتب.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * إنشاء حساب أدمن جديد لمدير محافظة
     */
    public function storeCityAdmin(Request $request)
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        $isAdmin = !empty($currentUser->is_admin) || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']);

        if (!$isAdmin) {
            abort(403, 'غير مصرح لك بإضافة أدمن المحافظات.');
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8',
            'city_role' => ['required', 'string', function ($attribute, $value, $fail) {
                $val = mb_strtolower(trim($value));
                if ($val !== 'admin' && $val !== 'ادمن' && !str_starts_with($val, 'admin_')) {
                    $fail('يرجى اختيار صلاحية أو محافظة صالحة.');
                }
            }],
        ], [
            'email.unique' => 'هذا البريد الإلكتروني مستخدم مسبقاً.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
        ]);

        $role = $request->city_role;
        $cityId = str_starts_with($role, 'admin_') ? str_replace('admin_', '', $role) : null;

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $role,
            'city_id'     => is_numeric($cityId) ? $cityId : null,
            'is_approved' => true,
        ]);

        return redirect()->back()->with('success', 'تم إنشاء وتعيين أدمن المحافظة بنجاح!');
    }

    /**
     * سحب صلاحية الأدمن وإعادة الحساب كمستخدم أو صاحب مكتب
     */
    public function destroyCityAdmin(User $user)
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        $isAdmin = !empty($currentUser->is_admin) || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']);

        if (!$isAdmin) {
            abort(403, 'غير مصرح لك بسحب الصلاحيات.');
        }

        $user->update(['role' => 'user']);

        return redirect()->back()->with('success', 'تم سحب صلاحيات الأدمن من الحساب بنجاح.');
    }

    /**
     * ترقية صاحب مكتب إلى أدمن عام أو أدمن محافظة
     */
    public function makeOfficeAdmin(Request $request)
    {
        $userId = $request->input('office_user_id') ?? $request->input('user_id');
        $role = $request->input('city_role');

        $user = User::findOrFail($userId);

        if (!$role) {
            $cityNameEn = strtolower(trim($user->city->name_en ?? $user->city->slug ?? ''));
            if (str_contains($cityNameEn, 'duhok') || str_contains($cityNameEn, 'دهوك')) {
                $role = 'admin_duhok';
            } elseif (str_contains($cityNameEn, 'erbil') || str_contains($cityNameEn, 'أربيل')) {
                $role = 'admin_erbil';
            } elseif (str_contains($cityNameEn, 'sul') || str_contains($cityNameEn, 'سليمانية')) {
                $role = 'admin_sulaymaniyah';
            } else {
                $role = 'admin_' . ($user->city_id ?? 'city');
            }
        }

        $user->role = $role;
        $user->is_admin = true;
        $user->save();

        return redirect()->back()->with('success', 'تم تعيين المكتب كـ أدمن بنجاح!');
    }

    // في الـ Controller المخصص لإدارة المكاتب لدى الأدمن (مثل Admin/OfficeController.php)

    /**
     * تحديث صلاحيات المحافظات المسموحة للمكتب العقاري
     */
    /**
     * تحديث المحافظات المسموحة للمكتب حتى إن كان لديه دور أدمن
     */
    public function update(Request $request, User $office)
    {
        $currentUser = auth()->user();
        $userRole = mb_strtolower(trim($currentUser->role ?? ''));

        $isAdmin = !empty($currentUser->is_admin) 
            || in_array($userRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) 
            || str_starts_with($userRole, 'admin_');

        if (!$isAdmin) {
            abort(403, 'غير مصرح لك بتعديل الصلاحيات.');
        }

        $request->validate([
            'allowed_cities'   => 'nullable|array',
            'allowed_cities.*' => 'exists:cities,id',
        ]);

        // تحديث المحافظات في جدول office_cities
        $office->allowedCities()->sync($request->input('allowed_cities', []));

        return redirect()->back()->with('success', app()->getLocale() == 'ku'
            ? 'دەسەڵاتێن پارێزگەهان ب سەرکەفتنی هاتنە نووکرن!'
            : 'تم تحديث صلاحيات المحافظات للمكتب بنجاح!');
    }

}