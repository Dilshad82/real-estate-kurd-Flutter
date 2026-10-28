<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Property extends Model
{
    use HasTranslations;

    /**
     * ==========================================
     * 1. الحقول المسموح بتعبئتها (Fillable)
     * - الشرح: إضافة user_id لربط العقار بحساب المكتب
     *   المسجل مباشرة إلى جانب الحقول الأساسية،
     *   وإضافة description و office_id للتفاصيل.
     * ==========================================
     */
    protected $fillable = [
        'user_id',
        'office_id',
        'city_id',
        'district',
        'type',
        'area_name',
        'area_size',
        'price',
        'rooms',
        'bathrooms',
        'image',
        'description', // تفاصيل وملاحظات إضافية عن العقار
        'features',
    ];

    /**
     * ==========================================
     * 2. إعدادات الترجمة والتحويل (Translatable & Casts)
     * ==========================================
     */
    public array $translatable = ['district'];

    protected $casts = [
        'features' => 'array',  
    ];

    /**
     * ==========================================
     * 3. العلاقات (Relationships)
     * ==========================================
     */

    // علاقة العقار بالحساب/المكتب (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة العقار بالكيان المستقل للمكتب إن وجد (Office)
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}