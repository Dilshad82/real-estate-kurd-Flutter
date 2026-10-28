<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LookupController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للبيانات المرجعية (عرض المحافظة الخاصة بالأدمن فقط)
     */
    public function index()
    {
        $user = auth()->user();

        // 1. جلب المحافظات حسب دور المستخدم
        if ($user->role === 'admin') {
            $cities = City::with('districts')->latest()->get();
        } else {
            $userCityId = $user->city_id ?? optional($user->city)->id;

            if ($userCityId) {
                $cities = City::with('districts')->where('id', $userCityId)->get();
            } else {
                $cityRole = str_replace('admin_', '', $user->role);
                $query = City::with('districts');

                if (is_numeric($cityRole)) {
                    $query->where('id', $cityRole);
                } else {
                    $query->where(function ($q) use ($cityRole) {
                        $q->where('name->en', 'like', "%{$cityRole}%")
                          ->orWhere('name->ar', 'like', "%{$cityRole}%")
                          ->orWhere('name->ku', 'like', "%{$cityRole}%");
                    });
                }

                $cities = $query->get();

                if ($cities->isEmpty()) {
                    $cities = City::with('districts')->latest()->get();
                }
            }
        }

        // 2. جلب أنواع العقارات
        $types = PropertyType::latest()->get();

        // 3. جلب كافة حسابات الأدمنية والمدراء (البحث بناءً على عمود role فقط لتجنب أخطاء قاعدة البيانات)
        $cityAdmins = User::where('role', 'like', '%admin%')
            ->latest()
            ->get();

        return view('admin.lookups', compact('cities', 'types', 'cityAdmins'));
    }

    // =========================================================================
    //  إدارة المحافظات / المدن (Cities) - حصرية للمدير العام (admin)
    // =========================================================================

    public function storeCity(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بإضافة المحافظات.');
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = trim($request->name);

        City::create([
            'name' => ['ar' => $name, 'ku' => $name, 'en' => $name],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تمت إضافة المحافظة بنجاح!');
    }

    public function updateCity(Request $request, City $city)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بتعديل المحافظات.');
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = trim($request->name);
        $locale = app()->getLocale();

        $currentName = is_array($city->name) ? $city->name : ['ar' => $city->name, 'ku' => $city->name, 'en' => $city->name];
        $currentName[$locale] = $name;

        $city->update([
            'name' => $currentName,
        ]);

        return redirect()->back()->with('success', 'تم تحديث المحافظة بنجاح!');
    }

    public function destroyCity(City $city)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بحذف المحافظات.');
        }

        $city->delete();
        return redirect()->back()->with('success', 'تم حذف المحافظة بنجاح!');
    }

    // =========================================================================
    //  إدارة المناطق (Districts)
    // =========================================================================

    public function storeDistrict(Request $request)
    {
        $user = auth()->user();

        // إذا كان المستخدم أدمن محافظة، يتم ربط المنطقة بمحافظته الحصرية تلقائياً
        if (str_starts_with($user->role, 'admin_')) {
            $cityRole = str_replace('admin_', '', $user->role);
            
            $query = City::query();
            if (is_numeric($cityRole)) {
                $query->where('id', $cityRole);
            } else {
                $query->where(function ($q) use ($cityRole) {
                    $q->where('name->en', 'like', "%{$cityRole}%")
                      ->orWhere('name->ar', 'like', "%{$cityRole}%")
                      ->orWhere('name->ku', 'like', "%{$cityRole}%");
                });
            }

            $city = $query->first();

            if (!$city) {
                $city = City::first();
            }

            $cityId = $city ? $city->id : $request->city_id;
            
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
        } else {
            $request->validate([
                'city_id' => 'required|exists:cities,id',
                'name'    => 'required|string|max:255',
            ]);
            $cityId = $request->city_id;
        }

        $name = trim($request->name);

        District::create([
            'city_id'   => $cityId,
            'name'      => ['ar' => $name, 'ku' => $name, 'en' => $name],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تمت إضافة المنطقة بنجاح!');
    }

    public function updateDistrict(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = trim($request->name);
        $locale = app()->getLocale();

        $currentName = is_array($district->name) ? $district->name : ['ar' => $district->name, 'ku' => $district->name, 'en' => $district->name];
        $currentName[$locale] = $name;

        $district->update([
            'name' => $currentName,
        ]);

        return redirect()->back()->with('success', 'تم تحديث المنطقة بنجاح!');
    }

    public function destroyDistrict(District $district)
    {
        $district->delete();
        return redirect()->back()->with('success', 'تم حذف المنطقة بنجاح!');
    }

    // =========================================================================
    //  إدارة أنواع العقارات (Property Types)
    // =========================================================================

    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = trim($request->name);

        PropertyType::create([
            'name'      => ['ar' => $name, 'ku' => $name, 'en' => $name],
            'slug'      => Str::slug($name) ?: 'type-' . time(),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تمت إضافة نوع العقار بنجاح!');
    }

    public function updateType(Request $request, PropertyType $type)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = trim($request->name);
        $locale = app()->getLocale();

        $currentName = is_array($type->name) ? $type->name : ['ar' => $type->name, 'ku' => $type->name, 'en' => $type->name];
        $currentName[$locale] = $name;

        $type->update([
            'name' => $currentName,
            'slug' => Str::slug($name) ?: $type->slug,
        ]);

        return redirect()->back()->with('success', 'تم تحديث نوع العقار بنجاح!');
    }

    public function destroyType(PropertyType $type)
    {
        $type->delete();
        return redirect()->back()->with('success', 'تم حذف نوع العقار بنجاح!');
    }

    // =========================================================================
    //  إدارة حسابات الأدمنية (إضافة، تعديل، وسحب صلاحية)
    // =========================================================================

    /**
     * حفظ أدمن جديد للنظام
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string',
        ], [
            'role.required' => 'يرجى اختيار الصلاحية / المحافظة المسندة.',
        ]);

        $role = $request->role;
        $cityId = null;

        // إذا تم اختيار أدمن محافظة
        if (str_starts_with($role, 'admin_')) {
            $cityId = str_replace('admin_', '', $role);
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
            'city_id'  => is_numeric($cityId) ? $cityId : null,
        ]);

        return redirect()->back()->with('success', 'تم إنشاء حساب الأدمن بنجاح!');
    }

    /**
     * تعديل بيانات الأدمن (الاسم والبريد والدور)
     */
    public function updateAdmin(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|string',
        ]);

        $role = $request->role;
        $cityId = null;

        if (str_starts_with($role, 'admin_')) {
            $cityId = str_replace('admin_', '', $role);
        }

        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $role,
            'city_id' => is_numeric($cityId) ? $cityId : null,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات الأدمن بنجاح!');
    }

    /**
     * سحب صلاحية الأدمن أو حذف المستخدم إذا لم يكن صاحب مكتب
     */
    public function revokeRole($id)
    {
        $user = User::findOrFail($id);

        // التحقق مما إذا كان المستخدم صاحب مكتب عقاري
        $isOfficeOwner = $user->office 
            || !empty($user->office_id) 
            || !empty($user->office_license) 
            || !empty($user->office_name);

        if ($isOfficeOwner) {
            // إعادة الحساب إلى دور صاحب مكتب بدون حذفه
            $user->update([
                'role' => 'office'
            ]);

            return redirect()->back()->with('success', app()->getLocale() == 'ku'
                ? 'دەسەڵاتی ئەدمین سڕایەوە و بەکارهێنەر وەک خاوەن نوسینگە مایەوە.'
                : 'تم سحب صلاحية الأدمن بنجاح وبقي المستخدم كصاحب مكتب عقاري.');
        }

        // إذا لم يكن صاحب مكتب، يتم حذفه نهائياً من قاعدة البيانات
        $user->delete();

        return redirect()->back()->with('success', app()->getLocale() == 'ku'
            ? 'بەکارهێنەرەکە بە تەواوی سڕایەوە.'
            : 'تم سحب الصلاحية وحذف المستخدم نهائياً من قاعدة البيانات.');
    }

    
}