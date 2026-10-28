<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\PropertyRequest;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| متحكم لوحة تحكم المكتب العقاري (OfficeDashboardController)
|--------------------------------------------------------------------------
| الوظيفة: إدارة شاشة حساب المكتب العقاري، حساب الإحصائيات السريعة (KPIs)،
| استقبال وإضافة وتعديل وحذف عقارات المكتب، وعرض طلبات العقارات المطابقة.
| متوافق بالكامل مع الهوية البصرية الحديثة والترجمة ثنائية اللغة (العربية والكردية).
|
*/

class OfficeDashboardController extends Controller
{
    /**
     * دالة مساعدة لاستخراج اسم الحقل (سواء كان كائن علاقة، أو JSON بلغات متعددة، أو نص عادي)
     * لتجنب ظهور كود الـ JSON الشجري في جدول الطلبات والعروض.
     */
    private function parseMultilingualValue($value, $lang)
    {
        if (is_null($value)) {
            return '-';
        }

        // 1. إذا كان الكائن من نوع Relation Object (مثل Model)
        if (is_object($value)) {
            if (isset($value->name)) {
                $name = $value->name;
                if (is_array($name)) {
                    return $name[$lang] ?? reset($name);
                }
                return $this->parseMultilingualValue($name, $lang);
            }
            if (isset($value->title)) {
                return $value->title;
            }
        }

        // 2. إذا كانت القيمة مصفوفة (Array)
        if (is_array($value)) {
            return $value[$lang] ?? $value['name'] ?? reset($value);
        }

        // 3. إذا كانت القيمة سلسلة نصية بتنسيق JSON
        if (is_string($value)) {
            $trimmed = trim($value);
            if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    if (isset($decoded['name'])) {
                        $name = $decoded['name'];
                        return is_array($name) ? ($name[$lang] ?? reset($name)) : $name;
                    }
                    return $decoded[$lang] ?? reset($decoded);
                }
            }
        }

        return $value;
    }

    /**
     * عرض الصفحة الرئيسية للوحة تحكم المكتب العقاري (offices.dashboard)
     * وتغذية الواجهة بالبيانات والإحصائيات الحيوية المنسقة.
     */
    public function index()
    {
        $user = auth()->user();
        $lang = app()->getLocale();

        $userRole = strtolower(trim($user->role ?? ''));

        // التحقق من الصلاحية (السماح لأصحاب المكاتب العقارية والأدمنية بالإدارة)
        if ($userRole !== 'office' && !str_contains($userRole, 'admin') && empty($user->is_admin)) {
            $errorMessage = $lang == 'ku' 
                ? 'رێگەپێدائی نینە بۆ چوونا ناڤ پەڕا نوسینگەیێن عەقاری.' 
                : 'غير مسموح لك بالوصول لصفحة المكاتب العقارية.';
            abort(403, $errorMessage);
        }

        // 1. جلب أحدث العقارات العامة لاستخدامها في شبكة العرض العام
        $properties = Property::latest()->take(8)->get();

        // 2. جلب عقارات المكتب الخاص للمستخدم الحالي
        $myProperties = Property::where('user_id', $user->id)->latest()->get();

        // 3. جلب الطلبات المطابقة بناءً على مدينة المكتب أو الأدمن
        $requestsQuery = PropertyRequest::with(['city', 'district', 'type']);
        if ($userRole !== 'admin' && $user->city_id) {
            $requestsQuery->where('city_id', $user->city_id);
        }
        $requests = $requestsQuery->latest()->paginate(10);

        // تنسيق قيم الجداول للترجمة السليمة
        $requests->getCollection()->transform(function ($item) use ($lang) {
            $item->display_name = $this->parseMultilingualValue($item->name ?? $item->user_name, $lang);
            $districtValue = $item->district ?? $item->area_name;
            $item->display_district = $this->parseMultilingualValue($districtValue, $lang);
            $rawType = $this->parseMultilingualValue($item->type ?? $item->property_type, $lang);
            $item->display_type = $this->formatPropertyType($rawType, $lang);
            return $item;
        });

        // 4. حساب بطاقات الإحصائيات الفورية (KPI Metrics) لبطاقات التصميم العلوي
        $stats = [
            'total_my_properties' => $myProperties->count(),
            'total_city_requests' => $requests->total(),
            'active_properties'   => $myProperties->where('is_active', true)->count() ?: $myProperties->count(),
            'match_rate'          => $myProperties->count() > 0 
                ? min(100, round(($requests->total() / $myProperties->count()) * 20)) 
                : 0,
        ];

        // إرسال كافة المتغيرات المنسقة إلى الواجهة
        return view('offices.dashboard', compact('requests', 'user', 'myProperties', 'properties', 'stats'));
    }

    /**
     * دالة مساعدة لترجمة وتنسيق نوع العقار بناءً على اللغة الحالية
     */
    private function formatPropertyType($type, $lang)
    {
        $typeLower = strtolower(trim($type));

        if (in_array($typeLower, ['apartment', 'شقة', 'شوقە', 'shk'])) {
            return $lang == 'ku' ? 'شوقە' : 'شقة';
        }
        if (in_array($typeLower, ['house', 'منزل', 'خانوو'])) {
            return $lang == 'ku' ? 'خانوو' : 'منزل';
        }
        if (in_array($typeLower, ['villa', 'فيلا', 'ڤێلا'])) {
            return $lang == 'ku' ? 'ڤێلا' : 'فيلا';
        }
        if (in_array($typeLower, ['land', 'أرض', 'عەرد'])) {
            return $lang == 'ku' ? 'عەرد' : 'أرض';
        }
        if (in_array($typeLower, ['office', 'مكتب', 'نوسینگە'])) {
            return $lang == 'ku' ? 'نوسینگە' : 'مكتب';
        }

        return $type;
    }

    /**
     * حفظ عقار جديد خاص بالمكتب (يدعم التخزين المائي والرسائل ثنائية اللغة)
     */
    public function storeProperty(Request $request)
    {
        $lang = app()->getLocale();

        // التحقق من صحة البيانات القادمة من النموذج
        $request->validate([
            'type'        => 'required|string',
            'area_name'   => 'required|string|max:255',
            'area_size'   => 'required|numeric|min:1',
            'price'       => 'required|numeric|min:0',
            'rooms'       => 'nullable|integer',
            'bathrooms'   => 'nullable|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user();

        // معالجة وحفظ الصورة في التخزين العام (public disk)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('properties', 'public');
        }

        // إنشاء سجل العقار في قاعدة البيانات
        Property::create([
            'user_id'     => $user->id,
            'city_id'     => $user->city_id,
            'type'        => $request->type,
            'area_name'   => $request->area_name,
            'area_size'   => $request->area_size,
            'price'       => $request->price,
            'rooms'       => $request->rooms ?? 0,
            'bathrooms'   => $request->bathrooms ?? 0,
            'image'       => $imagePath,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        $msg = $lang == 'ku' ? 'عەقار ب سەرکەفتیانە هاتە زێدەکرن!' : 'تم إضافة العقار بنجاح!';
        return redirect()->route('offices.dashboard')->with('success', $msg);
    }

    /**
     * تحديث بيانات عقار خاص بالمكتب (يدعم استبدال الصور وإدارة الكاش)
     */
    public function updateProperty(Request $request, $id)
    {
        $lang = app()->getLocale();

        // التأكد من وجود العقار وتبعيته للمستخدم الحالي حصراً
        $property = Property::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'type'        => 'required|string',
            'area_name'   => 'required|string|max:255',
            'area_size'   => 'required|numeric|min:1',
            'price'       => 'required|numeric|min:0',
            'rooms'       => 'nullable|integer',
            'bathrooms'   => 'nullable|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        // استبدال الصورة القديمة وحذفها من التخزين عند رفع صورة جديدة
        if ($request->hasFile('image')) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $property->image = $request->file('image')->store('properties', 'public');
        }

        // تحديث الحقول في قاعدة البيانات
        $property->update([
            'type'        => $request->type,
            'area_name'   => $request->area_name,
            'area_size'   => $request->area_size,
            'price'       => $request->price,
            'rooms'       => $request->rooms ?? 0,
            'bathrooms'   => $request->bathrooms ?? 0,
            'description' => $request->description,
        ]);

        $msg = $lang == 'ku' ? 'زانیاریێن عەقاری ب سەرکەفتیانە هاتنە نووکرن!' : 'تم تحديث بيانات العقار بنجاح!';
        return redirect()->route('offices.dashboard')->with('success', $msg);
    }

    /**
     * حذف عقار خاص بالمكتب وإزالة صوره المرتبطة من القرص
     */
    public function destroyProperty($id)
    {
        $lang = app()->getLocale();

        // التأكد من الملكية قبل الحذف
        $property = Property::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // حذف ملف الصورة من القرص الصلب
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        // حذف السجل من قاعدة البيانات
        $property->delete();

        $msg = $lang == 'ku' ? 'عەقار ب سەرکەفتیانە هاتە ژێبرن!' : 'تم حذف العقار بنجاح!';
        return redirect()->route('offices.dashboard')->with('success', $msg);
    }

    /**
     * تعيين أو تحويل حساب صاحب مكتب إلى أدمن محافظة
     */
    public function makeOfficeAdmin(Request $request)
    {
        $lang = app()->getLocale();

        $user = User::findOrFail($request->user_id);
        $cityName = strtolower($user->city->name_en ?? $user->city_name ?? 'city'); 

        $newRole = 'admin_' . $cityName;

        $user->update([
            'role' => $newRole
        ]);

        $cityNameDisplay = $user->city->name_ar ?? $cityName;
        $msg = $lang == 'ku'
            ? "پێگەهێ {$user->name} هاتە گوهۆڕین بۆ ئەدمینێ پارێزگەها ({$cityNameDisplay}) ب سەرکەفتیانە!"
            : "تم تعيين {$user->name} كـ أدمن لمحافظة ({$cityNameDisplay}) بنجاح!";

        return redirect()->back()->with('success', $msg);
    }
}