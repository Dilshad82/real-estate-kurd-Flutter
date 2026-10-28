<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\City;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * ==========================================
     * 1. عرض صفحة التسجيل (create)
     * - الشرح: جلب كافة المحافظات وتمريرها إلى
     *   واجهة التسجيل لتعبئة القائمة المنسدلة.
     * ==========================================
     */
    public function create(): View
    {
        $cities = City::all();
        return view('auth.register', compact('cities'));
    }

    /**
     * ==========================================
     * 2. معالجة وحفظ حساب المكتب العقاري الجديد (store)
     * - الشرح: التحقق من صحة البيانات وتثبيت دور
     *   الحساب تلقائياً كـ مكتب (office) مع إجبارية المحافظة
     *   ورفع ملف إجازة المكتب مع إبقائه غير مفعل لحين موافقة الآدمن.
     * ==========================================
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق من القواعد وشروط الإدخال (Validation)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // جعل اختيار المحافظة إجبارياً للتسجيل
            'city_id' => ['required', 'exists:cities,id'],

            // جعل رفع إجازة المكتب إجبارية (دعم جميع صيغ الصور الشهيرة + PDF مع زيادة المساحة لـ 5MB)
            'office_license' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        // معالجة ورفع ملف إجازة المكتب إلى مجلد التخزين
        $licensePath = null;
        if ($request->hasFile('office_license')) {
            $licensePath = $request->file('office_license')->store('licenses', 'public');
        }

        // إنشاء سجل المكتب العقاري الجديد في قاعدة البيانات
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            
            // تثبيت الدور تلقائياً ليكون مكتب عقاري (office)
            'role' => 'office',
            
            // إسناد رقم المحافظة المحددة من النموذج
            'city_id' => $request->city_id,

            // حفظ مسار إجازة المكتب العقاري
            'office_license' => $licensePath,

            // تعيين حالة الحساب ليكون غير مفعل تلقائياً بانتظار موافقة مدير النظام
            'is_approved' => false,
        ]);

        // إطلاق حدث التسجيل لمُعالجات لارافيل المعتادة (مثل بريد التفعيل)
        event(new Registered($user));

        // توجيه المستخدم إلى صفحة تسجيل الدخول مع رسالة تنبيه بانتظار موافقة الآدمن
        return redirect()->route('login')->with('status', 'تم تسجيل طلب حساب المكتب بنجاح! سيتم تفعيل حسابك بعد مراجعة وتدقيق إجازة المكتب من قبل مدير النظام.');
    }

    /**
     * ==========================================
     * 3. تحويل وتعيين حساب المكتب إلى أدمن المحافظة (makeOfficeAdmin)
     * - الشرح: قراءة المحافظة المرتبطة بالمكتب وتعيين
     *   دور الأدمن الخاص بتلك المحافظة تلقائياً (مثل: admin_erbil أو admin_duhok).
     * ==========================================
     */
    public function makeOfficeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::with('city')->findOrFail($request->user_id);

        // جدول مطابقة أسماء المحافظات المتاحة لتحويلها لرموز أدمن باللغة الإنجليزية
        $cityMapping = [
            'اربیل'      => 'erbil',
            'أربيل'      => 'erbil',
            'Erbil'      => 'erbil',
            'دهوك'      => 'duhok',
            'دهۆک'      => 'duhok',
            'Duhok'      => 'duhok',
            'السليمانية' => 'sulaymaniyah',
            'سلێمانی'    => 'sulaymaniyah',
            'Sulaymaniyah' => 'sulaymaniyah',
        ];

        // قراءة اسم المحافظة من علاقة المدينة (City) أو النص المخزن
        $cityName = trim($user->city->name_ar ?? $user->city->name ?? '');
        $cityKey = $cityMapping[$cityName] ?? 'general';

        // تحديث دور المستخدم ليكون أدمن المحافظة المحددة تلقائياً
        $user->update([
            'role' => 'admin_' . $cityKey,
        ]);

        return redirect()->back()->with('status', "تم تحويل حساب {$user->name} إلى أدمن محافظة بنجاح!");
    }
}