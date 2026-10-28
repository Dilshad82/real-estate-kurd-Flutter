<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Property;
use App\Models\PropertyRequest;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * حفظ طلب العقار المقدم من الزبون (الواجهة الرئيسية)
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'type_id' => 'required|exists:property_types,id',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'area_from' => 'nullable|numeric|min:0',
            'area_to' => 'nullable|numeric|min:0',
        ]);

        PropertyRequest::create([
            'client_name' => $request->name,
            'phone' => $request->phone,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'property_type_id' => $request->type_id,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'area_from' => $request->area_from,
            'area_to' => $request->area_to,
            'status' => 'pending',
        ]);

        $msg = app()->getLocale() == 'ku' 
            ? 'Daxwaza we bi serkeftî hate şandin!' 
            : 'تم إرسال طلبك بنجاح! سيتم التواصل معك عند توفر مطابقة.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * عرض جميع عقارات المكتب الحالي
     */
    public function index()
    {
        $office = auth()->user()->office;
        $properties = $office->properties()->with(['city', 'district', 'propertyType'])->latest()->get();

        return view('office.properties.index', compact('properties'));
    }

    /**
     * عرض صفحة إضافة عقار جديد للمكتب
     */
    public function create()
    {
        $cities = City::with('districts')->where('is_active', true)->get();
        $types = PropertyType::where('is_active', true)->get();

        return view('office.properties.create', compact('cities', 'types'));
    }

    /**
     * حفظ العقار الجديد للمكتب في قاعدة البيانات
     */
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:cities,id',
            'type_id' => 'required',
            'area_from' => 'nullable|numeric',
            'area_to' => 'nullable|numeric',
            'price_from' => 'nullable|numeric',
            'price_to' => 'nullable|numeric',
        ]);

        // 1. حفظ طلب الزبون ليظهر في لوحة تحكم مكتب المحافظة
        $propertyRequest = PropertyRequest::create($request->all());

        // 2. البحث عن العقارات المطابقة المضافة من قبل المكاتب
        $matchedProperties = Property::where('city_id', $request->city_id)
            ->when($request->filled('area_from'), fn($q) => $q->where('area_size', '>=', $request->area_from))
            ->when($request->filled('area_to'), fn($q) => $q->where('area_size', '<=', $request->area_to))
            ->when($request->filled('price_from'), fn($q) => $q->where('price', '>=', $request->price_from))
            ->when($request->filled('price_to'), fn($q) => $q->where('price', '<=', $request->price_to))
            ->with('user') // جلب بيانات المكتب صاحب العقار
            ->get();

        return back()->with([
            'success' => 'تم إرسال طلبك بنجاح! سيتم التواصل معك عند توفر العقار المناسب.',
            'matchedProperties' => $matchedProperties
        ]);
    }

    /**
     * عرض الطلبات المطابقة لعقارات المكتب
     */
    public function matchingRequests()
    {
        $office = auth()->user()->office;
        $properties = $office->properties;

        // استخراج الطلبات المطابقة بناءً على المحافظة والنوع والسعر
        $matchingRequests = PropertyRequest::whereIn('city_id', $properties->pluck('city_id'))
            ->whereIn('property_type_id', $properties->pluck('property_type_id'))
            ->latest()
            ->get();

        return view('office.matching-requests', compact('matchingRequests'));
    }
}