<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\City;
use App\Models\District;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\PropertyRequest;

class PropertyRequestForm extends Component
{
    public $client_name;
    public $client_phone;
    public $city_id;
    public $district_id;
    public $property_type_id;
    public $min_price;
    public $max_price;
    public $min_area;
    public $max_area;

    public $districts = [];
    public $matchedProperties = [];
    public $isSubmitted = false;

    public function updatedCityId($value)
    {
        if ($value) {
            $this->districts = District::where('city_id', $value)
                ->get()
                ->map(function ($district) {
                    $district->display_name = $this->extractName($district);
                    return $district;
                });
        } else {
            $this->districts = [];
        }
        $this->district_id = null;
    }

    /**
     * استخراج الاسم المترجم مباشرة من القيمة الخام المخزنة في قاعدة البيانات
     */
    private function extractName($model)
    {
        if (!$model) return 'غير محدد';

        // قراءة القيمة الخام مباشرة لضمان تجاوز أية أخطاء من حزم الترجمة
        $attributes = $model->getAttributes();
        $raw = $attributes['name'] ?? $model->name ?? null;

        if (empty($raw)) return 'غير محدد';

        // 1. إذا كانت القيمة مصفوفة جاهزة
        if (is_array($raw)) {
            return $raw['ar'] ?? $raw['ku'] ?? reset($raw) ?? 'غير محدد';
        }

        // 2. إذا كانت القيمة نصية
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);

            // معالجة النص المشفر مرتين (Double Encoded JSON)
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            if (is_array($decoded) && !empty($decoded)) {
                return $decoded['ar'] ?? $decoded['ku'] ?? reset($decoded) ?? 'غير محدد';
            }

            // إذا كان نصاً عادياً غير مشفر
            return $raw;
        }

        return 'غير محدد';
    }

    public function submitRequest()
    {
        $this->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'city_id' => 'required|exists:cities,id',
            'property_type_id' => 'required|exists:property_types,id',
        ]);

        PropertyRequest::create([
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'property_type_id' => $this->property_type_id,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_area' => $this->min_area,
            'max_area' => $this->max_area,
        ]);

        $query = Property::query()
            ->where('city_id', $this->city_id)
            ->where('property_type_id', $this->property_type_id)
            ->where('status', 'available');

        if ($this->district_id) {
            $query->where('district_id', $this->district_id);
        }

        if ($this->min_price) {
            $query->where('price', '>=', $this->min_price);
        }

        if ($this->max_price) {
            $query->where('price', '<=', $this->max_price);
        }

        $this->matchedProperties = $query->with('office')->get();
        $this->isSubmitted = true;
    }

    public function render()
    {
        $cities = City::all()->map(function ($city) {
            $city->display_name = $this->extractName($city);
            return $city;
        });

        $propertyTypes = PropertyType::all()->map(function ($type) {
            $type->display_name = $this->extractName($type);
            return $type;
        });

        return view('livewire.property-request-form', [
            'cities' => $cities,
            'propertyTypes' => $propertyTypes,
        ]);
    }
}