<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyRequest;
use App\Models\PropertyType;
use App\Models\User; // إضافة نموذج المستخدمين/المكاتب
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| متحكم طلبات العقارات والمكاتب (PropertyRequestController)
|--------------------------------------------------------------------------
| الوظيفة: 
| 1. عرض المكاتب العقارية التابعة للمحافظة المختارة (دهوك، أربيل...) مع أرقام هواتفها.
| 2. استقبال واستخراج طلبات الزوار للبحث الفوري عن العقارات المطابقة.
|
*/

class PropertyRequestController extends Controller
{
    /**
     * عرض صفحة المكاتب العقارية وتقديم الطلبات للمحافظة المختارة
     */
    public function create(Request $request, $city = null)
    {
        $cities = City::with('districts')->get();
        $types = PropertyType::all();

        $selectedCityId = null;

        // 1. إذا تم إرسال city_id عبر Query Parameter (مثال: ?city_id=1)
        if ($request->has('city_id')) {
            $selectedCityId = $request->city_id;
        } 
        // 2. إذا تم تمرير المحافظة كـ URL Parameter
        elseif ($city) {
            if (is_numeric($city)) {
                $selectedCityId = $city;
            } else {
                // البحث المباشر في قيم الـ JSON لجميع اللغات (en, ar, ku)
                $cityModel = City::where('name->en', 'like', "%{$city}%")
                    ->orWhere('name->ar', 'like', "%{$city}%")
                    ->orWhere('name->ku', 'like', "%{$city}%")
                    ->first();

                if ($cityModel) {
                    $selectedCityId = $cityModel->id;
                }
            }
        }

        // 3. التعيين الافتراضي للمحافظة الأولى إذا لم تتحدد
        if (!$selectedCityId && $cities->isNotEmpty()) {
            $selectedCityId = $cities->first()->id;
        }

        // 4. جلب المكاتب والأدمنية الذين لديهم صفة مكتب في هذه المحافظة
        // 4. جلب المكاتب والأدمنية المسجلين في المحافظة مع عقاراتهم
        $offices = User::where('city_id', $selectedCityId)
            ->where(function($q) {
                $q->where('role', 'office')
                ->orWhere('role', 'like', '%admin%')
                ->orWhere('role', 'company');
            })
            ->where('role', '!=', 'super_admin')
            ->withCount('properties')
            ->with(['properties' => function($query) {
                $query->latest(); // جلب أحدث العقارات المضافة من قبل المكتب
            }])
            ->get();

        return view('request-property', compact('cities', 'types', 'selectedCityId', 'offices'));
    }

    /**
     * حفظ طلب الزبون واستخراج العقارات المطابقة تلقائياً من المكاتب
     */
    public function store(Request $request)
    {
        $lang = app()->getLocale();

        // التحقق من صحة بيانات الطلب
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'city_id'    => 'required|exists:cities,id',
            'type_id'    => 'required',
            'area_from'  => 'nullable|numeric',
            'area_to'    => 'nullable|numeric',
            'price_from' => 'nullable|numeric',
            'price_to'   => 'nullable|numeric',
        ], [
            'name.required'    => $lang == 'ku' ? 'تکایە ناڤێ خۆ بنڤێسه.' : 'يرجى إدخال الاسم.',
            'phone.required'   => $lang == 'ku' ? 'تکایە ژمارا مۆبایلێ بنڤێسه.' : 'يرجى إدخال رقم الهاتف.',
            'city_id.required' => $lang == 'ku' ? 'تکایە پارێزگەهێ هه‌لبژێره.' : 'يرجى اختيار المحافظة.',
            'type_id.required' => $lang == 'ku' ? 'تکایە جۆرێ عەقاری هه‌لبژێره.' : 'يرجى اختيار نوع العقار.',
        ]);

        // 1. حفظ طلب الزبون في قاعدة البيانات
        $propertyRequest = PropertyRequest::create($request->all());

        // 2. البحث والتصفية الذكية للعقارات المطابقة المضافة من المكاتب
        $matchedQuery = Property::where('city_id', $request->city_id)
            ->when($request->filled('area_from'), fn($q) => $q->where('area_size', '>=', $request->area_from))
            ->when($request->filled('area_to'), fn($q) => $q->where('area_size', '<=', $request->area_to))
            ->when($request->filled('price_from'), fn($q) => $q->where('price', '>=', $request->price_from))
            ->when($request->filled('price_to'), fn($q) => $q->where('price', '<=', $request->price_to));

        // تصفية نوع العقار إن أمكن
        if ($request->filled('type_id')) {
            $typeModel = PropertyType::find($request->type_id);
            if ($typeModel) {
                $matchedQuery->where(function($q) use ($typeModel) {
                    $q->where('type', 'like', "%{$typeModel->slug}%")
                      ->orWhere('type', 'like', "%{$typeModel->name}%");
                });
            }
        }

        $matchedProperties = $matchedQuery->with('user')->latest()->get();

        $successMessage = $lang == 'ku'
            ? 'داواکاریا تە ب سەرکەفتیانە هاتە فرێکرن! دێ پەیوەندی ب تە هێتەکرن دەمێ عەقارێ گونجای بەردەست ببیت.'
            : 'تم إرسال طلبك بنجاح! سيتم التواصل معك عند توفر العقار المناسب.';

        return back()->with([
            'success'           => $successMessage,
            'matchedProperties' => $matchedProperties,
            'userRequest'       => $propertyRequest,
        ]);
    }
}