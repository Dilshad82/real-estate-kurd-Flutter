<?php

namespace App\Models;

// استخدام الكلاسات الأساسية من Laravel لإدارة المصادقة والإشعارات والـ Factories
// بەکارهێنانی کلاسی سەرەکی Laravel بۆ بەڕێوەبەری چوونەژوورەوە و ئاگادارکردنەوەکان
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * الحقول المسموح بتعبئتها وتعديلها بشكل مباشر (Mass Assignment)
     * ئەو بڕگانەی ڕێگەیان پێدراوە بە شێوەی ڕاستەوخۆ پڕبکرێنەوە
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',           // اسم المستخدم أو صاحب المكتب / ناوی بەکارهێنەر یان خاوەن نوسینگە
        'email',          // البريد الإلكتروني / ئیمەیڵ
        'password',       // كلمة المرور / وشەی نهێنی
        'role',           // دور المستخدم (admin, office, admin_erbil, admin_duhok, الخ) / ڕۆڵی بەکارهێنەر
        'city_id',        // معرف المحافظة التابع لها المكتب أو الأدمن / ناسنامەی پارێزگای پەیوەندیدار
        'office_license', // مسار ملف إجازة ممارسة المهنة / ڕێڕەوی بڕوانامە و مۆڵەتی نوسینگە
        'is_approved',    // حالة الموافقة على الحساب (true / false) / دۆخی پەسەندکردنی هەژمار
    ];

    /**
     * الحقول المخفية التي لا تظهر عند تحويل النموذج إلى Array أو JSON (مثل API)
     * ئەو بڕگانەی دەشاردرێنەوە لە کاتی ناردنی زانیارییەکان
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع البيانات الخاصة بالحقول تلقائياً (Attribute Casting)
     * گۆڕینی خۆکارانەی جۆری داتاکان
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',   // تشفير كلمة المرور تلقائياً / هۆنینەوەی خۆکارانەی وشەی نهێنی
            'is_approved'       => 'boolean',  // تحويل حقل الموافقة إلى قيمة منطقية (صح/خطأ) / گۆڕینی دۆخی پەسەندکردن بۆ Bolean
        ];
    }

    /**
     * معالجة الحفظ لمنع إرسال حقل is_admin إلى قاعدة البيانات عند التحديث
     * ڕێگریکردن لە ناردنی بڕگەی is_admin بۆ داتابەیس لە کاتی نوێکردنەوەدا
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            unset($user->is_admin);
        });
    }

    /* =========================================================================
     * 1. العلاقات (Relationships / پەیوەندییەکان)
     * ========================================================================= */

    /**
     * علاقة المستخدم بالمحافظة التابع لها (سواء كان أدمن محافظة أو صاحب مكتب)
     * پەیوەندی بەکارهێنەر بە پارێزگا (چ ئەدمینی پارێزگا بێت یان خاوەن نوسینگە)
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /* =========================================================================
     * 2. دۆخ و ڕێگەپێدانەکان (Functions for Admin / الأدمن والصلاحيات)
     * ========================================================================= */

    /**
     * فحص هل المستخدم يحمل صلاحية أدمن (عام أو أدمن محافظة)
     * پشکنین کە ئایا بەکارهێنەر ئەدمینە (گشتی یان ئەدمینی پارێزگا)
     */
    public function isAdmin(): bool
    {
        // يتفحص ما إذا كان دور المستخدم يساوي 'admin' أو يبدأ بـ 'admin_' مثل admin_erbil
        // پشکنین دەکات ئایا ڕۆڵی بەکارهێنەر 'admin'ە یان دەستپێدەکات بە '_admin' وەک admin_erbil
        return $this->role === 'admin' || str_starts_with($this->role, 'admin_');
    }

    /**
     * فحص هل المستخدم هو الأدمن العام (مدير النظام الرئيسي)
     * پشکنین کە ئایا بەکارهێنەر ئەدمینی گشتییە (بەڕێوەبەری سەرەکی سیستم)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * فحص هل الأدمن مسند إلى محافظة معينة (مثلاً: admin_erbil, admin_duhok, admin_sulaymaniyah)
     * پشکنین کە ئایا ئەدمینەکە تایبەتکراوە بە پارێزگایەکی دیاریکراو
     */
    public function isCityAdmin(): bool
    {
        return str_starts_with($this->role, 'admin_');
    }

    /* =========================================================================
     * 3. الدوال الخاصة بالمكتب العقاري (Office Owner Helpers / خاوەن نوسینگە)
     * ========================================================================= */

    /**
     * فحص هل المستخدم لديه صفة مكتب عقاري (حتى لو كان أدمن في نفس الوقت)
     * پشکنین کە ئایا بەکارهێنەر خاوەنی نوسینگەی خانوبەرەیە (تەنانەت ئەگەر ئەدمینیش بێت)
     */
    public function isOfficeOwner(): bool
    {
        // يكون صاحب مكتب إذا كان رول الحساب 'office' أو إذا رفع إجازة مكتب
        // خاوەن نوسینگەیە ئەگەر ڕۆڵی هەژمارەکەی 'office' بێت یان مۆڵەتی نوسینگەی هەبێت
        return $this->role === 'office' || !empty($this->office_license);
    }

    /**
     * فحص هل حساب المكتب مفعّل ومقبول من قبل الأدمن
     * پشکنین کە ئایا هەژماری نوسینگەکە پەسەندکراوە و چالاکە لەلایەن ئەدمینەوە
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    // المحافظات المسموح للمكتب إضافة/عرض عقاراته فيها
    public function allowedCities()
    {
        return $this->belongsToMany(City::class, 'office_cities', 'user_id', 'city_id');
    }

    /**
     * علاقة المستخدم/المكتب بالعقارات الخاصة به
     */
    public function properties()
    {
        return $this->hasMany(\App\Models\Property::class, 'user_id');
    }
}