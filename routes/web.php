<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Office\OfficeDashboardController;
use App\Http\Middleware\EnsureUserIsOffice;
use App\Http\Controllers\PropertyRequestController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LegalController;

/*
|--------------------------------------------------------------------------
| مسارات الشروط والأحكام وسياسة الخصوصية (Legal & App Store Requirements)
|--------------------------------------------------------------------------
| مسارات مفتوحة للعامة لتلبية متطلبات المتاجر الإلكترونية (Google Play / App Store).
|
*/

Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('terms.service');

/*
|--------------------------------------------------------------------------
| ملف المسارات الرئيسي للويب (routes/web.php)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. مسار تبديل لغة الموقع (Language Switcher)
// =========================================================================
Route::get('/lang/{lang}', function ($lang) {
    if (in_array($lang, ['ar', 'ku'])) {
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('lang.switch');


// =========================================================================
// 2. مسار تسجيل الخروج السريع (Force Logout)
// =========================================================================
Route::get('/logout-now', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('message', 'تم تسجيل الخروج بنجاح');
})->name('force.logout');


// =========================================================================
// 3. الصفحة الرئيسية للموقع (Landing Page)
// =========================================================================
Route::get('/', function () {
    $cities = \App\Models\City::all();
    return view('welcome', compact('cities'));
})->middleware('set_locale')->name('home');


// =========================================================================
// 4. صفحة تقديم الطلبات العقارية للزوار (Property Requests)
// =========================================================================
Route::get('/request-property/{city?}', [PropertyRequestController::class, 'create'])
    ->middleware('set_locale')
    ->name('property.request.page');

Route::get('/request/{city?}', [PropertyRequestController::class, 'create'])
    ->middleware('set_locale');

Route::get('/request-property/create', [PropertyRequestController::class, 'create'])
    ->middleware('set_locale')
    ->name('requests.create');

Route::post('/request-property', [PropertyRequestController::class, 'store'])
    ->middleware('set_locale')
    ->name('property.request.store');


// =========================================================================
// 5. التوجيه الذكي بعد تسجيل الدخول (Smart Dashboard Router)
// =========================================================================
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if (!$user) {
        return redirect()->route('login');
    }

    $userRole = strtolower(trim($user->role ?? ''));
    
    // أ) إذا كان الحساب يملك دور مكتب عقاري
    if ($userRole === 'office') {
        if (!$user->is_approved) {
            return redirect()->route('office.pending');
        }
        return redirect()->route('office.dashboard');
    }

    // ب) التحقق هل المستخدم أدمن عام أو أدمن محافظة
    $isAdmin = !empty($user->is_admin) 
        || $userRole === 'admin' 
        || str_starts_with($userRole, 'admin_');

    if ($isAdmin) {
        return redirect()->route('admin.lookups');
    }
    
    // ج) المسار الافتراضي
    return redirect()->route('office.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// =========================================================================
// 6. مسارات لوحة تحكم الإدارة (Admin Panel Group)
// =========================================================================
Route::middleware(['auth', 'set_locale'])->prefix('admin')->name('admin.')->group(function () {

    // أ. مسارات مشتركة (المدير العام + أدمنية المحافظات)
    Route::get('/lookups', [LookupController::class, 'index'])->name('lookups');
    
    Route::get('/cities', fn() => redirect()->route('admin.lookups'));
    Route::get('/districts', fn() => redirect()->route('admin.lookups'));
    Route::get('/types', fn() => redirect()->route('admin.lookups'));

    // إدارة المناطق (Districts)
    Route::post('/districts', [LookupController::class, 'storeDistrict'])->name('districts.store');
    Route::get('/districts/{district}/edit', [LookupController::class, 'editDistrict'])->name('districts.edit');
    Route::put('/districts/{district}', [LookupController::class, 'updateDistrict'])->name('districts.update');
    Route::delete('/districts/{district}', [LookupController::class, 'destroyDistrict'])->name('districts.destroy');

    // إدارة أنواع العقارات (Property Types)
    Route::post('/types', [LookupController::class, 'storeType'])->name('types.store');
    Route::get('/types/{type}/edit', [LookupController::class, 'editType'])->name('types.edit');
    Route::put('/types/{type}', [LookupController::class, 'updateType'])->name('types.update');
    Route::delete('/types/{type}', [LookupController::class, 'destroyType'])->name('types.destroy');

    // ب. مسارات حصرية للمدير العام الرئيسي فقط (Super Admin)
    Route::group(['middleware' => [function ($request, $next) {
        $user = Auth::user();
        $userRole = strtolower(trim($user->role ?? ''));
        
        $isSuperAdmin = !empty($user->is_admin) || $userRole === 'admin';

        if (!$isSuperAdmin) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة، هذه الصلاحية للمدير العام فقط.');
        }
        return $next($request);
    }]], function () {

        // إدارة المحافظات (Cities)
        Route::post('/cities', [LookupController::class, 'storeCity'])->name('cities.store');
        Route::get('/cities/{city}/edit', [LookupController::class, 'editCity'])->name('cities.edit');
        Route::put('/cities/{city}', [LookupController::class, 'updateCity'])->name('cities.update');
        Route::delete('/cities/{city}', [LookupController::class, 'destroyCity'])->name('cities.destroy');

        // إدارة المكاتب العقارية
        Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');
        Route::patch('/offices/{user}/approve', [OfficeController::class, 'approve'])->name('offices.approve');
        Route::patch('/offices/{user}/reject', [OfficeController::class, 'reject'])->name('offices.reject');

        // إدارة المدراء والأدمنية
        Route::post('/city-admins', [OfficeController::class, 'storeCityAdmin'])->name('city_admins.store');
        Route::post('/city-admins/make-office-admin', [OfficeController::class, 'makeOfficeAdmin'])->name('city_admins.make_office_admin');
        Route::put('/city-admins/{user}', [LookupController::class, 'updateAdmin'])->name('city_admins.update');
        Route::delete('/city-admins/{user}', [LookupController::class, 'revokeRole'])->name('city_admins.destroy');

    });
        
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        // مسار تحديث المحافظات للمكتب
    Route::put('/offices/{office}', [App\Http\Controllers\Admin\OfficeController::class, 'update'])->name('offices.update');
});
// =========================================================================
// 7. مسارات لوحة تحكم المكتب العقاري
// =========================================================================
Route::middleware(['auth', 'set_locale', EnsureUserIsOffice::class])->prefix('office')->group(function () {
    
    // صفحة الانتظار (تفتح للمكاتب غير المعتمدة)
    Route::get('/pending', function () {
        return view('offices.pending');
    })->name('office.pending');

    // لوحة التحكم المحمية بـ check.approval
    Route::middleware(['check.approval'])->group(function () {
        
        // [تعديل هام]: إضافة الاسم الشائع office.dashboard وتحديد المسار البديل لمنع الاستثناء
        Route::get('/dashboard', [OfficeDashboardController::class, 'index'])->name('office.dashboard');
        
        // ربط الاسم القديم offices.dashboard كمسار مساند لضمان توافق الأكواد القديمة
        Route::get('/main-dashboard', [OfficeDashboardController::class, 'index'])->name('offices.dashboard');
        
        Route::name('office.')->group(function () {
            Route::redirect('/properties', '/office/dashboard');
            Route::post('/properties', [OfficeDashboardController::class, 'storeProperty'])->name('properties.store');
            Route::put('/properties/{id}', [OfficeDashboardController::class, 'updateProperty'])->name('properties.update');
            Route::delete('/properties/{id}', [OfficeDashboardController::class, 'destroyProperty'])->name('properties.destroy');
        });

    });
});


// =========================================================================
// 8. مسارات إدارة الملف الشخصي (User Profile)
// =========================================================================
Route::middleware(['auth', 'set_locale'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// مسارات صفحات الفوتر الرسمية
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/terms', function () {
    return view('legal/terms-of-service');
})->name('terms');

Route::get('/privacy', function () {
    return view('legal/privacy-policy');
})->name('privacy');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

require __DIR__.'/auth.php';