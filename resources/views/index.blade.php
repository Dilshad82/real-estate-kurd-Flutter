<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.title') }}</title>
    <!-- استدعاء Tailwind CSS للتنسيق السريع والحديث -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-right">

    <!-- 1. الهيدر الموحد الخاص بجميع صفحات المنصة -->
    @include('layouts.header')

    <div class="max-w-4xl mx-auto my-10 p-4">
        
        <div class="bg-white p-8 rounded-xl shadow-md border overflow-visible relative z-10">
            <!-- عنوان النموذج الرئيسي -->
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">
                {{ __('site.request_title') }}
            </h1>

            <!-- 2. عرض تنبيه رسالة النجاح في حال تم حفظ الطلب بنجاح -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg text-center font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 3. عرض قائمة أخطاء التحقق (Validation Errors) إن وجدت -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 4. نموذج طلب عقار حسب المواصفات (إرسال عبر POST) -->
            <form action="{{ route('property.request.store') }}" method="POST" class="space-y-6">
                <!-- توكن حماية لارافيل الحاسم لحل ومنع خطأ 419 Page Expired -->
                @csrf

                <!-- الصف الأول: حقول إدخال اسم الزبون الكامل ورقم هاتف التواصل -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('site.full_name') }}
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('site.full_name') }}" class="w-full p-3 border rounded-lg text-right" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('site.phone') }}
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="07xxxxxxxxx" class="w-full p-3 border rounded-lg text-center dir-ltr" required>
                    </div>
                </div>

                <!-- الصف الثاني: القوائم المنسدلة (المحافظة، المنطقة التابعة، نوع العقار) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- قائمة اختيار المحافظة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('site.city') }}
                        </label>
                        <select name="city_id" id="city-select" class="w-full p-3 border rounded-lg bg-white cursor-pointer" required>
                            <option value="">{{ __('site.select_city') }}</option>
                            @foreach($cities as $city)
                                @php
                                    // التحقق من لغة اسم المحافظة (في حال كانت مصفوفة لغات متعددة)
                                    $cityName = is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name;
                                @endphp
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $cityName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- قائمة اختيار المنطقة (يتم تعبئتها ديناميكياً عبر JS عند اختيار المحافظة) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('site.district') }}
                        </label>
                        <select name="district_id" id="district-select" class="w-full p-3 border rounded-lg bg-white cursor-pointer" disabled>
                            <option value="">{{ __('site.select_city_first') }}</option>
                        </select>
                    </div>

                    <!-- قائمة اختيار نوع العقار (شقة، منزل، فيلا...) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('site.property_type') }}
                        </label>
                        <select name="type_id" class="w-full p-3 border rounded-lg bg-white cursor-pointer" required>
                            <option value="">{{ __('site.select_type') }}</option>
                            @foreach($types as $type)
                                @php
                                    $typeName = is_array($type->name) ? ($type->name[app()->getLocale()] ?? reset($type->name)) : $type->name;
                                @endphp
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $typeName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- الصف الثالث: نطاق المساحة ونطاق السعر المطلوب -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <!-- حقول المساحة (من - إلى) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 text-center">
                            {{ __('site.area') }}
                        </label>
                        <div class="flex space-x-2 space-x-reverse">
                            <input type="number" name="area_to" value="{{ old('area_to') }}" placeholder="{{ __('site.to') }}" class="w-1/2 p-2 border rounded-md text-center">
                            <input type="number" name="area_from" value="{{ old('area_from') }}" placeholder="{{ __('site.from') }}" class="w-1/2 p-2 border rounded-md text-center">
                        </div>
                    </div>

                    <!-- حقول السعر بالدولار (من - إلى) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 text-center">
                            {{ __('site.price') }}
                        </label>
                        <div class="flex space-x-2 space-x-reverse">
                            <input type="number" name="price_to" value="{{ old('price_to') }}" placeholder="{{ __('site.to') }}" class="w-1/2 p-2 border rounded-md text-center">
                            <input type="number" name="price_from" value="{{ old('price_from') }}" placeholder="{{ __('site.from') }}" class="w-1/2 p-2 border rounded-md text-center">
                        </div>
                    </div>
                </div>

                <!-- زر حفظ وإرسال طلب العقار -->
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition-colors">
                    {{ __('site.submit') }}
                </button>

            </form>

            <!-- ================================================================= -->
            <!-- 5. قسم جديد: عرض العقارات المطابقة المضافة من المكاتب للزبون -->
            <!-- ================================================================= -->
            @if(session('matchedProperties') && count(session('matchedProperties')) > 0)
                <div class="mt-10 border-t pt-8">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">🏠 عروض مطابقة لمواصفاتك من المكاتب العقارية</h2>
                        <p class="text-sm text-gray-500 mt-1">تم العثور على العقارات التالية بناءً على بحثك ورغبتك</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(session('matchedProperties') as $property)
                            <div class="bg-white rounded-xl border shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col justify-between">
                                <div>
                                    <!-- صورة العقار في حال تم رفعها من لوحة المكتب -->
                                    @if($property->image)
                                        <img src="{{ asset('storage/' . $property->image) }}" alt="صورة العقار" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                            📷 لا توجد صورة متوفرة
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <!-- نوع العقار وسعر العرض -->
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="bg-emerald-100 text-emerald-800 font-semibold text-xs px-3 py-1 rounded-full">{{ $property->type }}</span>
                                            <span class="font-bold text-xl text-emerald-600">${{ number_format($property->price) }}</span>
                                        </div>

                                        <!-- التفاصيل المكانية والمساحة -->
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <p>📍 <strong>المنطقة:</strong> {{ $property->area_name }}</p>
                                            <p>📐 <strong>المساحة:</strong> {{ $property->area_size }} م²</p>
                                            <p>🏢 <strong>المكتب العارض:</strong> {{ $property->user->name ?? 'مكتب عقاري' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- زر الربط المباشر مع رقم واتساب المكتب صاحب العقار -->
                                <div class="p-4 bg-gray-50 border-t">
                                    @php
                                        // أخذ رقم الواتساب المسجل إما من العقار أو من حساب المكتب
                                        $whatsapp = $property->whatsapp_number ?? ($property->user->phone ?? '');
                                        $message = urlencode("مرحباً، أبحث عن عقار ورأيت عرضكم المطابق لطلبي في منصة العقارات (نوع: " . $property->type . " - سعر: $" . $property->price . ")");
                                    @endphp
                                    <a href="https://wa.me/{{ $whatsapp }}?text={{ $message }}" 
                                       target="_blank" 
                                       class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg text-center flex items-center justify-center gap-2 transition-colors">
                                        💬 تواصل مباشر عبر الواتساب
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif(session('success') && (!session('matchedProperties') || count(session('matchedProperties')) == 0))
                <!-- تنبيه للزبون في حال عدم وجود عقارات مطابقة لحظياً -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-center text-sm">
                    ℹ️ تم تسجيل طلبك لدى المكاتب المعتمدة في محافظتك، وسيتم إشعارك فور قيام أي مكتب بإضافة عقار يطابق هذه المواصفات.
                </div>
            @endif

        </div>

    </div>

    <!-- 6. كود JavaScript تفاعلي لربط المحافظات بالمناطق ديناميكياً -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // تحويل بيانات المحافظات والمناطق من لارافيل إلى مصفوفة JSON
            const citiesData = @json($cities);
            const currentLocale = "{{ app()->getLocale() }}";

            const citySelect = document.getElementById('city-select');
            const districtSelect = document.getElementById('district-select');

            // دالة لتعبئة المناطق حسب المحافظة المختارة
            function populateDistricts(selectedCityId, selectedDistrictId = null) {
                const selectDistrictText = "{{ __('site.select_district') != 'site.select_district' ? __('site.select_district') : 'اختر المنطقة' }}";
                const selectCityFirstText = "{{ __('site.select_city_first') != 'site.select_city_first' ? __('site.select_city_first') : 'اختر المحافظة أولاً' }}";

                districtSelect.innerHTML = '';

                // إذا لم يتم اختيار محافظة، يتم تعطيل القائمة الثانية
                if (!selectedCityId) {
                    const opt = document.createElement('option');
                    opt.value = "";
                    opt.textContent = selectCityFirstText;
                    districtSelect.appendChild(opt);
                    districtSelect.disabled = true;
                    return;
                }

                // البحث عن المحافظة المحددة من المصفوفة
                const selectedCity = citiesData.find(city => city.id == selectedCityId);

                // إضافة المناطق التابعة للمحافظة إلى القائمة المنسدلة
                if (selectedCity && selectedCity.districts && selectedCity.districts.length > 0) {
                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = "";
                    defaultOpt.textContent = selectDistrictText;
                    districtSelect.appendChild(defaultOpt);

                    selectedCity.districts.forEach(district => {
                        let districtName = district.name;

                        // معالجة ترجمة الاسم في حال كان مخزناً بصيغة JSON
                        if (typeof district.name === 'object' && district.name !== null) {
                            districtName = district.name[currentLocale] || Object.values(district.name)[0];
                        } else if (typeof district.name === 'string' && district.name.startsWith('{')) {
                            try {
                                const parsed = JSON.parse(district.name);
                                districtName = parsed[currentLocale] || Object.values(parsed)[0];
                            } catch (e) {}
                        }

                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = districtName;
                        if (selectedDistrictId && district.id == selectedDistrictId) {
                            option.selected = true;
                        }
                        districtSelect.appendChild(option);
                    });

                    districtSelect.disabled = false;
                    districtSelect.removeAttribute('disabled');
                } else {
                    const noDataOpt = document.createElement('option');
                    noDataOpt.value = "";
                    noDataOpt.textContent = "لا توجد مناطق متاحة";
                    districtSelect.appendChild(noDataOpt);
                    districtSelect.disabled = true;
                }
            }

            // الاستماع لتغيير المحافظة لتعديل خيارات المناطق
            if (citySelect && districtSelect) {
                citySelect.addEventListener('change', function () {
                    populateDistricts(this.value);
                });

                // إعادة تعبئة المناطق في حال تم إعادة التوجيه بسبب خطأ في التحقق (Validation)
                const initialCityId = "{{ old('city_id') }}";
                const initialDistrictId = "{{ old('district_id') }}";
                if (initialCityId) {
                    populateDistricts(initialCityId, initialDistrictId);
                }
            }
        });
    </script>

</body>
</html>