<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'city_id',
        'district_id',
        'type_id',
        'area_from',
        'area_to',
        'price_from',
        'price_to',
        'min_bedrooms',
        'min_bathrooms',
        'preferred_features',
    ];

    protected $casts = [
        'preferred_features' => 'array',
    ];

    /**
     * العلاقة مع المحافظة
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * العلاقة مع المنطقة
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * العلاقة مع نوع العقار
     */
    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'type_id');
    }
}