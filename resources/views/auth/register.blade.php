<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- ==========================================
             1. حقل اسم المكتب العقاري (Office Name)
             - الشرح: تم تغيير التسمية لتوضيح إدخال اسم المكتب.
             ========================================== -->
        <div>
            <x-input-label for="name" value="اسم المكتب العقاري" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="مثال: مكتب أربيل العقاري" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- ==========================================
             2. حقل البريد الإلكتروني (Email Address)
             ========================================== -->
        <div class="mt-4">
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- ==========================================
             3. حقل كلمة السر (Password)
             ========================================== -->
        <div class="mt-4">
            <x-input-label for="password" value="كلمة السر" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- ==========================================
             4. حقل تأكيد كلمة السر (Confirm Password)
             ========================================== -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="تأكيد كلمة السر" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- ==========================================
             5. حقل اختيار المحافظة (City Selection)
             - الشرح: مرسوم بالترجمة المعتمدة ثنائية اللغة (عربي/كردي)
               وهو حقل إجباري مخصص للمكتب بدون تكرار أو JS.
             ========================================== -->
        <div class="mt-4">
            <x-input-label for="city_id" value="المحافظة (الخاصة بالمكتب)" />
            
            <select id="city_id" name="city_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">-- اختر المحافظة --</option>
                @foreach($cities as $city)
                    @php
                        // فحص واستخراج الاسم المترجم بناءً على اللغة الحالية للموقع
                        $cityName = is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name;
                    @endphp
                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $cityName }}</option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
        </div>

        <!-- ==========================================
             6. حقل إجازة/رخصة المكتب (Office License)
             - الشرح: حقل رفع ملف إجازة المكتب العقاري لتدقيقها من قبل مدير النظام.
             ========================================== -->
        <div class="mt-4">
            <x-input-label for="office_license" value="صورة إجازة / رخصة المكتب العقاري" />
            
            <input id="office_license" type="file" name="office_license" accept="image/*,.pdf" class="block mt-1 w-full text-sm text-gray-500 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required />
            <p class="text-xs text-gray-500 mt-1">يرجى رفع صورة أو ملف PDF واضح لرخصة المكتب لكي يتم التفعيل من المدير.</p>

            <x-input-error :messages="$errors->get('office_license')" class="mt-2" />
        </div>

        <!-- ==========================================
             7. أزرار التحكم والتسجيل (Action Buttons)
             - الشرح: زر تسجبل الحساب مخصص للمكاتب العقارية.
             ========================================== -->
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                لديك حساب بالفعل؟
            </a>

            <x-primary-button class="ms-4">
                تسجيل مكتب جديد
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>