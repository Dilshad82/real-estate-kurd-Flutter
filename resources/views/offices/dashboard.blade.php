<x-app-layout>
    {{-- 
        ====================================================================================
        الحاوية الرئيسية للصفحة (Main Page Wrapper)
        - استخدام خلفية slate-50 المعتمدة في نظام الألوان الحديث.
        - اتجاه الكتابة RTL مع دعم كامل للغتين العربية والكردية.
        ====================================================================================
    --}}
    <div class="py-8 bg-slate-50 min-h-screen" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- ==================================================================================== --}}
            {{-- 1. قسم الرسائل والتنبيهات (Success & Validation Error Messages) --}}
            {{-- ==================================================================================== --}}
            
            {{-- تنبيه النجاح --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl shadow-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-base shrink-0">
                            ✓
                        </div>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg px-2 rounded-lg hover:bg-emerald-100/50 transition cursor-pointer">&times;</button>
                </div>
            @endif

            {{-- أخطاء الإدخال --}}
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200/80 text-rose-800 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-xs shrink-0">
                            !
                        </div>
                        <p class="font-bold text-sm">
                            {{ app()->getLocale() == 'ku' ? 'تکایە ئاگاداری ڤان خاڵان ببه:' : 'يرجى تصحيح الأخطاء التالية:' }}
                        </p>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 me-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ==================================================================================== --}}
            {{-- 2. بطاقة معلومات المكتب العقارية بتدرج فخم (Hero Header Section) --}}
            {{-- ==================================================================================== --}}
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    
                    {{-- تفاصيل المكتب --}}
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-2xl shadow-inner text-indigo-300 shrink-0">
                            🏢
                        </div>
                        <div>
                            <h2 class="text-xl font-bold tracking-normal leading-relaxed text-white">
                                {{ auth()->user()->name === 'مدير النظام' ? __('site.system_admin') : (auth()->user()->office_name ?? auth()->user()->name) }}
                            </h2>
                            <p class="text-xs text-slate-300 mt-1 flex items-center gap-2 font-medium">
                                <span class="inline-flex items-center gap-1">📍 {{ app()->getLocale() == 'ku' ? 'پارێزگەھ:' : 'المحافظة:' }} 
                                    <strong class="text-white">
                                        @php
                                            $userCity = auth()->user()->city ?? null;
                                            $cityName = null;

                                            if ($userCity) {
                                                if (is_array($userCity->name)) {
                                                    $cityName = $userCity->name[app()->getLocale()] ?? reset($userCity->name);
                                                } elseif (is_string($userCity->name)) {
                                                    $decoded = json_decode($userCity->name, true);
                                                    $cityName = is_array($decoded) ? ($decoded[app()->getLocale()] ?? reset($decoded)) : $userCity->name;
                                                }
                                            }

                                            if (!$cityName) {
                                                $cityName = auth()->user()->governorate ?? auth()->user()->city_name ?? (app()->getLocale() == 'ku' ? 'نەهاتیە دیارکرن' : 'غير محدد');
                                            }
                                        @endphp
                                        {{ $cityName }}
                                    </strong>
                                </span>
                                <span class="text-slate-600">•</span>
                                <span class="inline-flex items-center gap-1 dir-ltr">📞 {{ auth()->user()->phone ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- شارة معتمد --}}
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-xl border border-white/15 shadow-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-bold text-emerald-300">
                            {{ app()->getLocale() == 'ku' ? 'نوسینگەها مۆڵەتدامالکری' : 'مكتب عقاري معتمد' }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- ==================================================================================== --}}
            {{-- 3. بطاقات الإحصائيات السريعة (KPI Metric Cards) --}}
            {{-- ==================================================================================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                {{-- بطاقة 1: إجمالي عقارات المكتب --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1">{{ app()->getLocale() == 'ku' ? 'عەقارێن من' : 'عقاراتي المضافة' }}</p>
                        <h4 class="text-xl font-black text-slate-900">{{ $stats['total_my_properties'] ?? (isset($myProperties) ? $myProperties->count() : 0) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        🏠
                    </div>
                </div>

                {{-- بطاقة 2: الطلبات الواردة هذا الشهر --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1">{{ app()->getLocale() == 'ku' ? 'داواکاریێن پارێزگەهێ' : 'طلبات المحافظة' }}</p>
                        <h4 class="text-xl font-black text-slate-900">{{ $stats['total_city_requests'] ?? (isset($requests) ? $requests->total() : 0) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        📋
                    </div>
                </div>

                {{-- بطاقة 3: العقارات النشطة --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1">{{ app()->getLocale() == 'ku' ? 'عەقارێن چالاک' : 'العقارات النشطة' }}</p>
                        <h4 class="text-xl font-black text-emerald-600">{{ $stats['active_properties'] ?? (isset($myProperties) ? $myProperties->count() : 0) }}</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        ●
                    </div>
                </div>

                {{-- بطاقة 4: نسبة المطابقة العقارية --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1">{{ app()->getLocale() == 'ku' ? 'ڕێژەیا گونجاندنێ' : 'نسبة المطابقة' }}</p>
                        <h4 class="text-xl font-black text-indigo-600">{{ $stats['match_rate'] ?? 85 }}%</h4>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        ⚡
                    </div>
                </div>

            </div>

            {{-- ==================================================================================== --}}
            {{-- 4. نموذج إضافة عقار جديد لمكتبك (New Property Creation Form) --}}
            {{-- ==================================================================================== --}}
            <div class="bg-white overflow-hidden shadow-xs rounded-2xl p-6 border border-slate-200/80">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>{{ app()->getLocale() == 'ku' ? 'زێدەکرنا عەقارەکێ نوی' : 'إضافة عقار جديد لمكتبك' }}</span>
                    </h3>
                </div>

                <form action="{{ route('office.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf 

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        
                        {{-- 1. اختيار المحافظة (من المحافظات المسموحة للمكتب من الأدمن) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'پارێزگەھ' : 'المحافظة' }} <span class="text-rose-500">*</span>
                            </label>
                            
                            @php
                                $allowedCities = auth()->user()->allowedCities;
                                if ($allowedCities->isEmpty() && auth()->user()->city) {
                                    $allowedCities = collect([auth()->user()->city]);
                                }
                            @endphp

                            <select name="city_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                                @forelse($allowedCities as $city)
                                    @php
                                        $cName = null;
                                        if (is_array($city->name)) {
                                            $cName = $city->name[app()->getLocale()] ?? reset($city->name);
                                        } elseif (is_string($city->name)) {
                                            $decoded = json_decode($city->name, true);
                                            $cName = is_array($decoded) ? ($decoded[app()->getLocale()] ?? reset($decoded)) : $city->name;
                                        }
                                    @endphp
                                    <option value="{{ $city->id }}">{{ $cName }}</option>
                                @empty
                                    <option value="">{{ app()->getLocale() == 'ku' ? 'نەهاتیە دیارکرن' : 'لا توجد محافظات مسموحة' }}</option>
                                @endforelse
                            </select>
                        </div>

                        {{-- 2. اختيار نوع العقار --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری' : 'نوع العقار' }} <span class="text-rose-500">*</span>
                            </label>
                            <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                                <option value="apartment">{{ app()->getLocale() == 'ku' ? 'شوقە' : 'شقة' }}</option>
                                <option value="house">{{ app()->getLocale() == 'ku' ? 'خانوو' : 'منزل' }}</option>
                                <option value="villa">{{ app()->getLocale() == 'ku' ? 'ڤێلا' : 'فيلا' }}</option>
                                <option value="land">{{ app()->getLocale() == 'ku' ? 'عەرد' : 'أرض' }}</option>
                                <option value="office">{{ app()->getLocale() == 'ku' ? 'نوسینگە' : 'مكتب' }}</option>
                            </select>
                        </div>

                        {{-- 3. إدخال اسم المنطقة أو الحي --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'تاخ / دەڤەر' : 'المنطقة / الحي' }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="area_name" value="{{ old('area_name') }}" placeholder="{{ app()->getLocale() == 'ku' ? 'ب بۆچوون: دهۆک - ماسێک' : 'مثال: دهوك - ماسيك' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                        </div>

                        {{-- 4. المساحة بالمتر المربع --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'رووبەر (م²)' : 'المساحة (م²)' }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="area_size" value="{{ old('area_size') }}" placeholder="150" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                        </div>

                        {{-- 5. السعر بالدولار المنسق بألوان emerald --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'بها ($)' : 'السعر ($)' }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" placeholder="50000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-emerald-600 font-extrabold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                        </div>

                        {{-- 6. عدد الغرف --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'ژمارا ژۆران' : 'عدد الغرف' }}
                            </label>
                            <input type="number" name="rooms" value="{{ old('rooms', 0) }}" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                        </div>

                        {{-- 7. عدد الحمامات --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'ژمارا تەوالێتان' : 'عدد الحمامات' }}
                            </label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms', 0) }}" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                        </div>

                    </div>

                    {{-- 8. وصف وتفاصيل العقار --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'تێبینی و زانیاریێن زێدەتر' : 'تفاصيل وملاحظات عن العقار' }}
                        </label>
                        <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition resize-none">{{ old('description') }}</textarea>
                    </div>

                    {{-- 9. اختيار صورة العقار والمعاينة --}}
                    <div class="flex flex-col md:flex-row items-center gap-4 bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                        <div class="w-full">
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ app()->getLocale() == 'ku' ? 'وێنێ عەقاری' : 'صورة العقار' }}
                            </label>
                            <input type="file" name="image" accept="image/*" onchange="previewPropertyImage(this)" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition cursor-pointer">
                        </div>
                        
                        <div id="image_preview_container" class="hidden w-20 h-16 rounded-xl border border-dashed border-slate-300 overflow-hidden relative shrink-0">
                            <img id="image_preview" src="#" alt="معاينة" class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- زر الإرسال --}}
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-7 py-3 rounded-xl text-xs transition shadow-xs flex items-center gap-2 cursor-pointer">
                            <span>✨</span>
                            <span>{{ app()->getLocale() == 'ku' ? 'بەلاڤکرنا عەقاری' : 'نشر العقار' }}</span>
                        </button>
                    </div>

                </form>
            </div>

            {{-- ==================================================================================== --}}
            {{-- 5. جدول العقارات المعروضة الخاصة بالمكتب (My Active Properties Table) --}}
            {{-- ==================================================================================== --}}
            <div class="bg-white overflow-hidden shadow-xs rounded-2xl border border-slate-200/80">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">
                            {{ app()->getLocale() == 'ku' ? 'عەقارێن من ئێن بەلاڤکری' : 'عقاراتي المعروضة المضافة من قبل مكتبك' }}
                        </h3>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold">
                        {{ app()->getLocale() == 'ku' ? 'ژمارە:' : 'العدد:' }} {{ isset($myProperties) ? $myProperties->count() : 0 }}
                    </span>
                </div>

                @if(isset($myProperties) && $myProperties->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs text-slate-600">
                            <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 text-center">{{ app()->getLocale() == 'ku' ? 'وێنە' : 'الصورة' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری' : 'نوع العقار' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'تاخ / دەڤەر' : 'المنطقة / الحي' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'رووبەر (م²)' : 'المساحة (م²)' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'بها ($)' : 'السعر ($)' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'ژۆر / تەوالێت' : 'الغرف / الحمامات' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'مێژووا بەلاڤکرنێ' : 'تاريخ النشر' }}</th>
                                    <th class="px-6 py-4 text-center">{{ app()->getLocale() == 'ku' ? 'کریار' : 'الإجراءات' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($myProperties as $prop)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        
                                        {{-- 1. الصورة المصغرة --}}
                                        <td class="px-6 py-3.5 text-center">
                                            @if($prop->image)
                                                <img src="{{ asset('storage/' . $prop->image) }}" alt="وێنێ عەقاری" class="w-12 h-12 object-cover rounded-xl border border-slate-200 mx-auto shadow-xs">
                                            @else
                                                <span class="inline-block text-[10px] text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">{{ app()->getLocale() == 'ku' ? 'وێنە نینە' : 'لا توجد صورة' }}</span>
                                            @endif
                                        </td>

                                        {{-- 2. نوع العقار --}}
                                        <td class="px-6 py-3.5 font-bold text-slate-900">
                                            @if($prop->type == 'apartment' || $prop->type == 'شقة' || $prop->type == 'شوقە') 
                                                {{ app()->getLocale() == 'ku' ? 'شوقە' : 'شقة' }}
                                            @elseif($prop->type == 'house' || $prop->type == 'منزل' || $prop->type == 'خانوو') 
                                                {{ app()->getLocale() == 'ku' ? 'خانوو' : 'منزل' }}
                                            @elseif($prop->type == 'villa' || $prop->type == 'فيلا' || $prop->type == 'ڤێلا') 
                                                {{ app()->getLocale() == 'ku' ? 'ڤێلا' : 'فيلا' }}
                                            @elseif($prop->type == 'land' || $prop->type == 'أرض' || $prop->type == 'عەرد') 
                                                {{ app()->getLocale() == 'ku' ? 'عەرد' : 'أرض' }}
                                            @elseif($prop->type == 'office' || $prop->type == 'مكتب' || $prop->type == 'نوسینگە') 
                                                {{ app()->getLocale() == 'ku' ? 'نوسینگە' : 'مكتب' }}
                                            @else 
                                                {{ $prop->type }} 
                                            @endif
                                        </td>

                                        {{-- 3. اسم المنطقة --}}
                                        <td class="px-6 py-3.5 font-medium text-slate-700">
                                            {{ $prop->area_name ?? $prop->district ?? (app()->getLocale() == 'ku' ? 'نەهاتیە دیارکرن' : 'غير محدد') }}
                                        </td>

                                        {{-- 4. المساحة --}}
                                        <td class="px-6 py-3.5 font-semibold text-slate-700">
                                            {{ $prop->area_size ?? $prop->area ?? '0' }} م²
                                        </td>

                                        {{-- 5. السعر المنسق بـ emerald --}}
                                        <td class="px-6 py-3.5 text-emerald-600 font-extrabold dir-ltr text-right">
                                            ${{ number_format($prop->price ?? 0) }}
                                        </td>

                                        {{-- 6. الغرف والحمامات --}}
                                        <td class="px-6 py-3.5 text-xs text-slate-500 font-medium">
                                            {{ $prop->rooms ?? $prop->bedrooms ?? 0 }} {{ app()->getLocale() == 'ku' ? 'ژۆر' : 'غرف' }} / {{ $prop->bathrooms ?? 0 }} {{ app()->getLocale() == 'ku' ? 'تەوالێت' : 'حمام' }}
                                        </td>

                                        {{-- 7. تاريخ النشر --}}
                                        <td class="px-6 py-3.5 text-xs text-slate-400">
                                            {{ $prop->created_at ? $prop->created_at->diffForHumans() : '-' }}
                                        </td>

                                        {{-- 8. أزرار الإجراءات --}}
                                        <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                
                                                <button type="button" 
                                                        onclick="openEditModal({{ json_encode($prop) }})" 
                                                        class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1 cursor-pointer">
                                                    ✏️ {{ app()->getLocale() == 'ku' ? 'دەستکاری' : 'تعديل' }}
                                                </button>

                                                <form action="{{ route('office.properties.destroy', $prop->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ku' ? 'تۆ پشتراستی ژ ژێبرنا ڤی عەقاری؟' : 'هل أنت متأكد من رغبتك في حذف هذا العقار؟' }}');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs px-3 py-1.5 rounded-lg font-bold transition flex items-center gap-1 cursor-pointer">
                                                        🗑️ {{ app()->getLocale() == 'ku' ? 'ژێبرن' : 'حذف' }}
                                                    </button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 bg-slate-50/50 text-slate-500 rounded-xl text-center text-xs">
                        {{ app()->getLocale() == 'ku' ? 'تە ع عەقارێن نوی زێدە نه‌کرينە. فۆزما سەری بکاربینە بۆ زێدەکرنا ئێکەم عەقار.' : 'لم تقم بإضافة أي عقارات خاصة بمكتبك حتى الآن. استخدم النموذج أعلاه لإضافة أول عقار.' }}
                    </div>
                @endif
            </div>

            {{-- ==================================================================================== --}}
            {{-- 6. جدول طلبات البحث عن العقارات الواردة لمحافظة هذا المكتب (Incoming Requests) --}}
            {{-- ==================================================================================== --}}
            <div class="bg-white overflow-hidden shadow-xs rounded-2xl border border-slate-200/80">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">
                            {{ app()->getLocale() == 'ku' ? 'داواکاریێن هاتی بۆ پارێزگەها تە' : 'طلبات العقارات الواردة لمحافظتك' }}
                        </h3>
                    </div>
                </div>

                @php
                    $incomingList = isset($incomingRequests) ? $incomingRequests : (isset($requests) ? $requests : collect());
                    $lang = app()->getLocale();
                @endphp

                @if($incomingList->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs text-slate-600">
                            <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'ناڤ' : 'الاسم' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'دەڤەر / تاخ' : 'المنطقة' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری' : 'نوع العقار' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'رووبەر (م²)' : 'المساحة (م²)' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'مۆبایل' : 'رقم الهاتف' }}</th>
                                    <th class="px-6 py-4">{{ app()->getLocale() == 'ku' ? 'مێژوو' : 'التاريخ' }}</th>
                                    <th class="px-6 py-4 text-center">{{ app()->getLocale() == 'ku' ? 'پەیوەندی' : 'التواصل المباشر' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($incomingList as $req)
                                    @php
                                        $extractValue = function($input) use ($lang) {
                                            if (is_null($input)) return '-';

                                            if (is_object($input)) {
                                                if (isset($input->name)) return is_array($input->name) ? ($input->name[$lang] ?? reset($input->name)) : $input->name;
                                                if (isset($input->title)) return $input->title;
                                            }

                                            if (is_array($input)) {
                                                return $input[$lang] ?? $input['name'] ?? reset($input);
                                            }

                                            if (is_string($input)) {
                                                $trimmed = trim($input);
                                                if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                                                    $decoded = json_decode($trimmed, true);
                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                        if (isset($decoded['name'])) {
                                                            return is_array($decoded['name']) ? ($decoded['name'][$lang] ?? reset($decoded['name'])) : $decoded['name'];
                                                        }
                                                        return $decoded[$lang] ?? reset($decoded);
                                                    }
                                                }
                                            }

                                            return $input;
                                        };

                                        $nameVal = $extractValue($req->name ?? $req->user_name ?? null);
                                        $districtVal = $extractValue($req->district ?? $req->area_name ?? null);
                                        $typeVal = $extractValue($req->propertyType ?? $req->property_type ?? $req->type ?? null);
                                        
                                        $whatsappPhone = preg_replace('/[^0-9]/', '', $req->phone ?? '');
                                        $whatsappMsg = rawurlencode("مرحباً {$nameVal}، قمنا بمراجعة طلبك بخصوص {$typeVal} في منطقة {$districtVal} عبر المنصة العقارية ولدينا عروض مناسبة لك!");
                                    @endphp

                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-3.5 font-bold text-slate-900">
                                            {{ $nameVal }}
                                        </td>
                                        
                                        <td class="px-6 py-3.5 font-medium text-slate-700">
                                            {{ $districtVal }}
                                        </td>

                                        <td class="px-6 py-3.5 font-semibold text-slate-700">
                                            @if(in_array($typeVal, ['apartment', 'شقة', 'شوقە', 'shk'])) 
                                                {{ $lang == 'ku' ? 'شوقە' : 'شقة' }}
                                            @elseif(in_array($typeVal, ['house', 'منزل', 'خانوو'])) 
                                                {{ $lang == 'ku' ? 'خانوو' : 'منزل' }}
                                            @elseif(in_array($typeVal, ['villa', 'فيلا', 'ڤێلا'])) 
                                                {{ $lang == 'ku' ? 'ڤێلا' : 'فيلا' }}
                                            @elseif(in_array($typeVal, ['land', 'أرض', 'عەرد'])) 
                                                {{ $lang == 'ku' ? 'عەرد' : 'أرض' }}
                                            @elseif(in_array($typeVal, ['office', 'مكتب', 'نوسینگە'])) 
                                                {{ $lang == 'ku' ? 'نوسینگە' : 'مكتب' }}
                                            @else 
                                                {{ $typeVal }} 
                                            @endif
                                        </td>

                                        <td class="px-6 py-3.5 font-semibold text-slate-700">
                                            {{ $req->area ?? $req->area_size ?? '-' }} م²
                                        </td>

                                        <td class="px-6 py-3.5 font-bold text-indigo-600 dir-ltr text-right">
                                            {{ $req->phone ?? '-' }}
                                        </td>

                                        <td class="px-6 py-3.5 text-xs text-slate-400">
                                            {{ $req->created_at ? $req->created_at->diffForHumans() : '-' }}
                                        </td>

                                        <td class="px-6 py-3.5 text-center whitespace-nowrap">
                                            @if(!empty($whatsappPhone))
                                                <a href="https://wa.me/{{ $whatsappPhone }}?text={{ $whatsappMsg }}" 
                                                   target="_blank" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition border border-emerald-200/60 shadow-xs">
                                                    <span>💬</span>
                                                    <span>{{ $lang == 'ku' ? 'واتساپ' : 'مراسلة واتساب' }}</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 bg-slate-50/50 text-slate-500 rounded-xl text-center text-xs">
                        {{ app()->getLocale() == 'ku' ? 'چ داواکاریێن نوی بۆ پارێزگەها تە نەهاتینە.' : 'لا توجد طلبات عقارات واردة لمحافظتك حالياً.' }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ==================================================================================== --}}
    {{-- 7. النافذة المنبثقة لتعديل بيانات العقار (Edit Property Modal Window) --}}
    {{-- ==================================================================================== --}}
    <div id="editPropertyModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden transform transition-all border border-slate-100">
            
            {{-- رأس النافذة المنبثقة --}}
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-5 flex justify-between items-center border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <h3 class="font-bold text-sm tracking-wide">
                        {{ app()->getLocale() == 'ku' ? 'دەستکاریکرنا زانیاریێن عەقاری' : 'تعديل بيانات العقار' }}
                    </h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-xl font-bold px-2 rounded-lg hover:bg-slate-800 transition cursor-pointer">&times;</button>
            </div>
            
            {{-- نموذج تعديل البيانات --}}
            <form id="editPropertyForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- تعديل المحافظة المسموحة --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'پارێزگەھ' : 'المحافظة' }}
                        </label>
                        @php
                            $allowedCities = auth()->user()->allowedCities;
                            if ($allowedCities->isEmpty() && auth()->user()->city) {
                                $allowedCities = collect([auth()->user()->city]);
                            }
                        @endphp
                        <select name="city_id" id="edit_city_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                            @foreach($allowedCities as $city)
                                @php
                                    $cName = null;
                                    if (is_array($city->name)) {
                                        $cName = $city->name[app()->getLocale()] ?? reset($city->name);
                                    } elseif (is_string($city->name)) {
                                        $decoded = json_decode($city->name, true);
                                        $cName = is_array($decoded) ? ($decoded[app()->getLocale()] ?? reset($decoded)) : $city->name;
                                    }
                                @endphp
                                <option value="{{ $city->id }}">{{ $cName }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- تعديل نوع العقار --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری' : 'نوع العقار' }}
                        </label>
                        <select name="type" id="edit_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                            <option value="apartment">{{ app()->getLocale() == 'ku' ? 'شوقە' : 'شقة' }}</option>
                            <option value="house">{{ app()->getLocale() == 'ku' ? 'خانوو' : 'منزل' }}</option>
                            <option value="villa">{{ app()->getLocale() == 'ku' ? 'ڤێلا' : 'فيلا' }}</option>
                            <option value="land">{{ app()->getLocale() == 'ku' ? 'عەرد' : 'أرض' }}</option>
                            <option value="office">{{ app()->getLocale() == 'ku' ? 'نوسینگە' : 'مكتب' }}</option>
                        </select>
                    </div>

                    {{-- تعديل اسم المنطقة --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'تاخ / دەڤەر' : 'المنطقة / الحي' }}
                        </label>
                        <input type="text" name="area_name" id="edit_area_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                    </div>

                    {{-- تعديل المساحة --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'رووبەر (م²)' : 'المساحة (م²)' }}
                        </label>
                        <input type="number" step="0.01" name="area_size" id="edit_area_size" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition" required>
                    </div>

                    {{-- تعديل السعر --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'بها ($)' : 'السعر ($)' }}
                        </label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-emerald-600 font-extrabold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                    </div>

                    {{-- تعديل عدد الغرف --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'ژمارا ژۆران' : 'عدد الغرف' }}
                        </label>
                        <input type="number" name="rooms" id="edit_rooms" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                    </div>

                    {{-- تعديل عدد الحمامات --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'ژمارا تەوالێتان' : 'عدد الحمامات' }}
                        </label>
                        <input type="number" name="bathrooms" id="edit_bathrooms" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                    </div>

                </div>

                {{-- تعديل الوصف --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        {{ app()->getLocale() == 'ku' ? 'تێبینی و زانیاریێن زێدەتر' : 'تفاصيل وملاحظات' }}
                    </label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition resize-none"></textarea>
                </div>

                {{-- تغيير الصورة --}}
                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-200/60">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        {{ app()->getLocale() == 'ku' ? 'تۆمارکرنا وێنەکێ نوی (ئەگەر تە دڤێت بگوھۆڕی)' : 'تغيير الصورة (اختياري)' }}
                    </label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition cursor-pointer">
                </div>

                {{-- أزرار حفظ أو إلغاء --}}
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-xl font-bold transition cursor-pointer">
                        {{ app()->getLocale() == 'ku' ? 'پاشگەزبوون' : 'إلغاء' }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-xl font-bold transition shadow-xs cursor-pointer">
                        {{ app()->getLocale() == 'ku' ? 'پاشکەوتکرن' : 'حفظ التعديلات' }}
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ==================================================================================== --}}
    {{-- 8. دوال الجافاسكريبت المساعدة (JavaScript Functions) --}}
    {{-- ==================================================================================== --}}
    <script>
        /**
         * 1. دالة عرض معاينة الصورة المختارة فورياً في نموذج الإضافة
         */
        function previewPropertyImage(input) {
            const previewContainer = document.getElementById('image_preview_container');
            const previewImage = document.getElementById('image_preview');

            if (input && input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImage) {
                        previewImage.src = e.target.result;
                    }
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /**
         * 2. دالة فتح نافذة التعديل (Modal) وتعبئة الحقول بقيم العقار المحدد تلقائياً
         */
        function openEditModal(property) {
            if (!property) return;

            const form = document.getElementById('editPropertyForm');
            if (form) {
                form.action = `/office/properties/${property.id}`;
            }

            const cityInput = document.getElementById('edit_city_id');
            if (cityInput) cityInput.value = property.city_id || '';

            const typeInput = document.getElementById('edit_type');
            if (typeInput) typeInput.value = property.type || 'apartment';

            const areaNameInput = document.getElementById('edit_area_name');
            if (areaNameInput) areaNameInput.value = property.area_name || property.district || '';

            const areaSizeInput = document.getElementById('edit_area_size');
            if (areaSizeInput) areaSizeInput.value = property.area_size || property.area || '';

            const priceInput = document.getElementById('edit_price');
            if (priceInput) priceInput.value = property.price || '';

            const roomsInput = document.getElementById('edit_rooms');
            if (roomsInput) roomsInput.value = property.rooms || property.bedrooms || 0;

            const bathroomsInput = document.getElementById('edit_bathrooms');
            if (bathroomsInput) bathroomsInput.value = property.bathrooms || 0;

            const descInput = document.getElementById('edit_description');
            if (descInput) descInput.value = property.description || '';

            const modal = document.getElementById('editPropertyModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        /**
         * 3. دالة إغلاق النافذة المنبثقة للتعديل
         */
        function closeEditModal() {
            const modal = document.getElementById('editPropertyModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>