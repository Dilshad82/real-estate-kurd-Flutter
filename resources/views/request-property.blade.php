<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ku' ? 'نوسینگه‌هێن عه‌قاری و لێگەڕیانا عه‌قاری' : 'المكاتب العقارية والبحث عن عقار' }}</title>

    {{-- خط Cairo للغة الكردية والعربية لتفادي تفكك الحروف --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tailwind CDN & FontAwesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- Alpine.js للتحكم بالفلترة والنافذة المنبثقة تفاعلياً --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, html, body, button, input, select {
            font-family: 'Cairo', system-ui, -apple-system, sans-serif !important;
            letter-spacing: 0 !important;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-right font-sans antialiased" 
      x-data="{ 
          selectedType: '', 
          openOfficeId: null, 
          selectedOfficeName: '', 
          officeProperties: [] 
      }">

    {{-- ========================================================================= --}}
    {{-- 1. الهيدر العلوي: للتنقل وتغيير اللغة                                      --}}
    {{-- ========================================================================= --}}
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto p-4 flex justify-between items-center">
            
            <a href="{{ route('home') }}" class="text-indigo-600 font-bold text-xs hover:text-indigo-800 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-right rtl:rotate-0 ltr:rotate-180"></i>
                <span>{{ app()->getLocale() == 'ku' ? 'زڤڕین بۆ سەرەکی' : 'العودة للصفحة الرئيسية' }}</span>
            </a>

            <div class="flex items-center gap-3">
                @if(app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'ku') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition">
                        <span>🌐</span>
                        <span>کوردی (Badinî)</span>
                    </a>
                @else
                    <a href="{{ route('lang.switch', 'ar') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition">
                        <span>🌐</span>
                        <span>العربية</span>
                    </a>
                @endif

                <h2 class="text-sm font-black text-slate-800 hidden sm:block">
                    {{ app()->getLocale() == 'ku' ? 'پلاتفۆڕما عەقاراتێن کوردستانێ' : 'منصة عقارات إقليم كردستان' }}
                </h2>
            </div>

        </div>
    </header>

    <div class="max-w-6xl mx-auto p-4 sm:p-6 my-6 space-y-10">

        {{-- ========================================================================= --}}
        {{-- 2. شريط المحافظة المختارة                                                  --}}
        {{-- ========================================================================= --}}
        @php
            $selectedCity = $cities->firstWhere('id', $selectedCityId);
            $cityName = $selectedCity 
                ? (is_array($selectedCity->name) ? ($selectedCity->name[app()->getLocale()] ?? reset($selectedCity->name)) : $selectedCity->name) 
                : (app()->getLocale() == 'ku' ? 'نەهاتیە دیارکرن' : 'غير محددة');
        @endphp

        <div class="bg-indigo-600 text-white rounded-3xl p-6 shadow-lg flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3 text-right">
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl shrink-0">
                    📍
                </div>
                <div>
                    <span class="text-xs text-indigo-200 block font-medium">
                        {{ app()->getLocale() == 'ku' ? 'پارێزگەها نوکە هاتیە دەستنیشانکرن:' : 'نطاق البحث والمكاتب المعروضة حالياً:' }}
                    </span>
                    <h1 class="text-xl md:text-2xl font-black">
                        {{ app()->getLocale() == 'ku' ? 'نوسینگه‌هێن عه‌قاری ل ' : 'المكاتب العقارية المعتمدة في ' }}
                        <span class="text-yellow-300">{{ $cityName }}</span>
                    </h1>
                </div>
            </div>

            <a href="{{ route('home') }}" class="bg-white hover:bg-indigo-50 text-indigo-700 text-xs px-5 py-3 rounded-2xl font-bold transition shadow-sm shrink-0 flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>{{ app()->getLocale() == 'ku' ? 'گوهۆڕینا پارێزگەھێ' : 'تغيير المحافظة' }}</span>
            </a>
        </div>

        {{-- ========================================================================= --}}
        {{-- 3. قسم عرض كروت المكاتب مع الفلترة حسب نوع العقار (شقق، منازل...)            --}}
        {{-- ========================================================================= --}}
        <div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg md:text-xl font-black text-slate-900">
                        {{ app()->getLocale() == 'ku' ? '🏢 نوسینگه‌هێن باوه‌رپێکراو' : '🏢 المكاتب العقارية المتاحة' }}
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">
                        {{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری هەلبژێرە دا کو ب تنێ ژمارا ڤى جۆری بهێتە نیشاندان' : 'حدد نوع العقار المطلوب لفلترة المكاتب وعرض العدد المطابق فوراً' }}
                    </p>
                </div>

                {{-- 🎯 الفلتر المباشر أعلى المكاتب --}}
                <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-600 me-1">
                        <i class="fa-solid fa-filter text-indigo-600"></i>
                        {{ app()->getLocale() == 'ku' ? 'فلترکرن:' : 'تصفية المكاتب حسب:' }}
                    </span>
                    <select x-model="selectedType" class="bg-slate-50 text-slate-800 text-xs font-bold p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        <option value="">{{ app()->getLocale() == 'ku' ? 'هەموو عەقارات (شوقە، خانوو...)' : 'جميع العقارات (شقق، منازل...)' }}</option>
                        @foreach($types as $type)
                            @php
                                $typeName = is_array($type->name) ? ($type->name[app()->getLocale()] ?? reset($type->name)) : $type->name;
                            @endphp
                            <option value="{{ $type->name }}">{{ $typeName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(isset($offices) && count($offices) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($offices as $office)
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md hover:border-indigo-300 transition flex flex-col justify-between"
                             x-data="{ 
                                 props: {{ json_encode($office->properties) }},
                                 get filteredCount() {
                                     if (!selectedType) return this.props.length;
                                     return this.props.filter(p => p.type && p.type.includes(selectedType)).length;
                                 }
                             }">
                            <div>
                                {{-- اسم وشعار المكتب --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg border border-indigo-100 shrink-0">
                                        {{ mb_substr($office->office_name ?? $office->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-sm">
                                            {{ $office->office_name ?? $office->name }}
                                        </h3>
                                        <span class="text-[11px] text-slate-500 font-medium block">
                                            📍 {{ $cityName }}
                                        </span>
                                    </div>
                                </div>

                                {{-- 🎯 زر عدد العقارات: يعرض الرقم المفلتر حسب نوع العقار المحدد --}}
                                <button type="button" 
                                        @click="openOfficeId = {{ $office->id }}; selectedOfficeName = '{{ $office->office_name ?? $office->name }}'; officeProperties = props"
                                        class="w-full bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 p-3 rounded-2xl border border-slate-100 transition cursor-pointer group flex items-center justify-between px-4 mb-4">
                                    <span class="text-xs text-slate-600 font-bold group-hover:text-indigo-700">
                                        <span x-text="selectedType ? 'عدد ' + selectedType + ' المعروضة:' : '{{ app()->getLocale() == 'ku' ? 'ژمارا عەقاران:' : 'عدد العقارات المعروضة:' }}'"></span>
                                    </span>
                                    <span class="bg-indigo-600 text-white text-xs font-black px-2.5 py-1 rounded-xl shadow-xs group-hover:scale-105 transition"
                                          x-text="filteredCount">
                                    </span>
                                </button>
                            </div>

                            {{-- أزرار الواتساب والاتصال --}}
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $office->phone ?? '');
                                $waMessage = rawurlencode("مرحباً، أود الاستفسار عن العقارات المتاحة لديكم في منصة عقارات كردستان");
                            @endphp
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                                @if(!empty($cleanPhone))
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank"
                                       class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold py-2.5 px-3 rounded-xl text-center text-xs transition flex items-center justify-center gap-1.5">
                                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                        <span>{{ app()->getLocale() == 'ku' ? 'واتساپ' : 'واتساب' }}</span>
                                    </a>
                                    <a href="tel:{{ $cleanPhone }}"
                                       class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold py-2.5 px-3 rounded-xl text-center text-xs transition flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-phone text-indigo-600 text-xs"></i>
                                        <span>{{ app()->getLocale() == 'ku' ? 'پەیوەندی' : 'اتصال' }}</span>
                                    </a>
                                @else
                                    <div class="col-span-2 text-center text-xs text-slate-400 py-1 font-medium">
                                        {{ app()->getLocale() == 'ku' ? 'ژمارا مۆبایلێ نینە' : 'رقم الهاتف غير متاح' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white p-8 rounded-3xl border border-slate-200/80 text-center text-slate-500 space-y-2">
                    <span class="text-3xl block">🏢</span>
                    <p class="font-bold text-sm">
                        {{ app()->getLocale() == 'ku' ? 'تشتەک نینە ل ڤێ پارێزگەهێ نوکە' : 'لا توجد مكاتب مسجلة حالياً في هذه المحافظة' }}
                    </p>
                </div>
            @endif
        </div>

        {{-- ========================================================================= --}}
        {{-- 4. محرك البحث المباشر دون الحاجة لاسم أو رقم هاتف                           --}}
        {{-- ========================================================================= --}}
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xs border border-slate-200/80">
            <div class="text-center mb-8">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 font-bold text-xs rounded-full inline-block mb-2">
                    🔍 {{ app()->getLocale() == 'ku' ? 'لێگەڕیانا ئێكسه‌ر' : 'البحث السريع المباشر' }}
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                    {{ app()->getLocale() == 'ku' ? 'لێگەڕيان ل تەڤایا عەقاراتێن نوسینگه‌ها ل ' . $cityName : 'ابحث في كافة عقارات المكاتب في ' . $cityName }}
                </h2>
            </div>

            <form action="{{ route('property.request.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="city_id" id="city-select" value="{{ $selectedCityId }}">
                <input type="hidden" name="name" value="زائر / لێگەڕيان">
                <input type="hidden" name="phone" value="0000000000">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'جۆرێ عەقاری' : 'نوع العقار' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="type_id" required class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                            <option value="">{{ app()->getLocale() == 'ku' ? 'هەموو جۆرێن عەقاران' : 'جميع أنواع العقارات (شقة، منزل...)' }}</option>
                            @foreach($types as $type)
                                @php
                                    $typeName = is_array($type->name) ? ($type->name[app()->getLocale()] ?? reset($type->name)) : $type->name;
                                @endphp
                                <option value="{{ $type->id }}">{{ $typeName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            {{ app()->getLocale() == 'ku' ? 'تاخ / دەڤەر' : 'المنطقة / الحي' }}
                        </label>
                        <select name="district_id" id="district-select" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition cursor-pointer">
                            <option value="">{{ app()->getLocale() == 'ku' ? 'هەموو تاخێن ' . $cityName : 'جميع مناطق ' . $cityName }}</option>
                        </select>
                    </div>
                </div>

                {{-- المساحة والسعر --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 text-center">
                            {{ app()->getLocale() == 'ku' ? 'رووبەر (م²)' : 'المساحة (م²)' }}
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="area_from" placeholder="{{ app()->getLocale() == 'ku' ? 'ژ' : 'من' }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs text-center">
                            <input type="number" name="area_to" placeholder="{{ app()->getLocale() == 'ku' ? 'بۆ' : 'إلى' }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs text-center">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2 text-center">
                            {{ app()->getLocale() == 'ku' ? 'بها ($)' : 'السعر المتاح ($)' }}
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="price_from" placeholder="{{ app()->getLocale() == 'ku' ? 'ژ' : 'من' }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs text-center font-bold text-emerald-600">
                            <input type="number" name="price_to" placeholder="{{ app()->getLocale() == 'ku' ? 'بۆ' : 'إلى' }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs text-center font-bold text-emerald-600">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition shadow-md text-xs flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>{{ app()->getLocale() == 'ku' ? 'لێگەڕیانا عەقاراتێن گونجای ل تەڤایا نوسینگه‌ها' : 'البحث عن العقارات المطابقة في كافة المكاتب' }}</span>
                </button>
            </form>

            {{-- نتائج البحث --}}
            @if(session('matchedProperties'))
                <div class="mt-10 border-t border-slate-100 pt-8">
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-base font-black text-slate-900 text-center">
                            {{ app()->getLocale() == 'ku' ? '🏠 ئەنجامێن لێگەڕيانێ ژ نوسینگه‌هان (' . session('matchedProperties')->count() . ' عەقار)' : '🏠 نتائج البحث المطابقة من المكاتب العقارية (' . session('matchedProperties')->count() . ' عقار)' }}
                        </h3>
                    </div>

                    @if(session('matchedProperties')->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach(session('matchedProperties') as $property)
                                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-indigo-300 transition">
                                    <div>
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-black text-base text-emerald-600 dir-ltr">${{ number_format($property->price) }}</span>
                                            <span class="bg-indigo-50 text-indigo-700 text-[11px] font-bold px-2.5 py-1 rounded-lg border border-indigo-200/60">
                                                {{ $property->type }}
                                            </span>
                                        </div>

                                        <div class="space-y-1 text-xs text-slate-600 font-medium">
                                            <p class="flex items-center gap-1">
                                                <span>📍 {{ app()->getLocale() == 'ku' ? 'تاخ:' : 'المنطقة:' }}</span>
                                                <strong class="text-slate-800">{{ $property->area_name ?? $cityName }}</strong>
                                            </p>
                                            <p class="flex items-center gap-1">
                                                <span>📐 {{ app()->getLocale() == 'ku' ? 'رووبەر:' : 'المساحة:' }}</span>
                                                <strong class="text-slate-800">{{ $property->area_size }} م²</strong>
                                            </p>
                                            <p class="flex items-center gap-1 text-slate-500 pt-1">
                                                <span>🏢 {{ app()->getLocale() == 'ku' ? 'نوسینگە:' : 'المكتب:' }}</span>
                                                <span class="font-bold text-indigo-600">{{ $property->user->office_name ?? $property->user->name ?? 'مكتب عقاري' }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    @php
                                        $officePhone = preg_replace('/[^0-9]/', '', $property->user->phone ?? '');
                                        $waText = rawurlencode("مرحباً، أستفسر عن " . $property->type . " المتاح لديكم في " . ($property->area_name ?? $cityName));
                                    @endphp

                                    @if(!empty($officePhone))
                                        <a href="https://wa.me/{{ $officePhone }}?text={{ $waText }}" target="_blank" class="mt-4 w-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 font-bold py-2.5 rounded-xl text-center text-xs transition flex items-center justify-center gap-2 shadow-xs">
                                            <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                                            <span>{{ app()->getLocale() == 'ku' ? 'پەیوەندیا ئێکسەر ب واتساپێ' : 'تواصل مباشر مع المكتب صاحب العرض' }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-500 font-bold text-xs bg-slate-50 rounded-2xl border border-slate-100">
                            {{ app()->getLocale() == 'ku' ? 'مخابن! چ عەقارێن دەستنیشانکری نەهاتنە لێگەڕيان.' : 'لم نجد عقارات مطابقة تماماً لمواصفات بحثك في الوقت الحالي.' }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>

    {{-- ========================================================================= --}}
    {{-- 🌟 النافذة المنبثقة (Modal) لعرض العقارات المفلترة للمكتب المحدد               --}}
    {{-- ========================================================================= --}}
    <div x-show="openOfficeId !== null" 
         x-transition.opacity
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="openOfficeId = null" 
             class="bg-white rounded-3xl w-full max-w-3xl max-h-[85vh] overflow-hidden shadow-2xl flex flex-col border border-slate-100">
            
            <div class="p-5 bg-indigo-600 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🏢</span>
                    <h3 class="font-black text-sm md:text-base">
                        {{ app()->getLocale() == 'ku' ? 'عەقاراتێن نوسینگەیێ:' : 'عقارات مكتب:' }}
                        <span x-text="selectedOfficeName" class="text-yellow-300"></span>
                    </h3>
                </div>
                <button @click="openOfficeId = null" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-sm transition">
                    ✕
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                <template x-if="officeProperties && officeProperties.length > 0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="item in officeProperties" :key="item.id">
                            <div x-show="!selectedType || (item.type && item.type.includes(selectedType))" 
                                 class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 hover:border-indigo-300 transition space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-black text-emerald-600 text-sm" x-text="'$' + Number(item.price || 0).toLocaleString()"></span>
                                    <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-lg" x-text="item.type"></span>
                                </div>
                                <div class="text-xs text-slate-600 space-y-1 pt-1 font-medium">
                                    <p class="flex items-center gap-1">
                                        <span>📍 {{ app()->getLocale() == 'ku' ? 'دەڤەر:' : 'المنطقة:' }}</span>
                                        <strong class="text-slate-800" x-text="item.area_name || item.address || '{{ $cityName }}'"></strong>
                                    </p>
                                    <p class="flex items-center gap-1" x-show="item.area_size">
                                        <span>📐 {{ app()->getLocale() == 'ku' ? 'رووبەر:' : 'المساحة:' }}</span>
                                        <strong class="text-slate-800" x-text="item.area_size + ' م²'"></strong>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!officeProperties || officeProperties.length === 0">
                    <div class="text-center py-10 text-slate-400 space-y-2">
                        <span class="text-4xl block">🏚️</span>
                        <p class="text-xs font-bold">
                            {{ app()->getLocale() == 'ku' ? 'چ عەقار نینن ل ڤێ نوكێ' : 'لا توجد عقارات مضافة حالياً لهذا المكتب' }}
                        </p>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 text-center shrink-0">
                <button @click="openOfficeId = null" class="bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold px-6 py-2.5 rounded-xl transition">
                    {{ app()->getLocale() == 'ku' ? 'داخستن' : 'إغلاق' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Script جلب المناطق المباشر --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const citiesData = @json($cities);
            const currentLocale = "{{ app()->getLocale() }}";
            const citySelect = document.getElementById('city-select');
            const districtSelect = document.getElementById('district-select');

            function populateDistricts(selectedCityId) {
                districtSelect.innerHTML = '<option value="">' + (currentLocale === 'ku' ? 'هەموو تاخێن ' + "{{ $cityName }}" : 'جميع مناطق ' + "{{ $cityName }}") + '</option>';
                const selectedCity = citiesData.find(city => city.id == selectedCityId);

                if (selectedCity && selectedCity.districts && selectedCity.districts.length > 0) {
                    selectedCity.districts.forEach(district => {
                        let districtName = district.name;
                        if (typeof district.name === 'object' && district.name !== null) {
                            districtName = district.name[currentLocale] || Object.values(district.name)[0];
                        }
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = districtName;
                        districtSelect.appendChild(option);
                    });
                    districtSelect.disabled = false;
                } else {
                    districtSelect.disabled = true;
                }
            }

            if (citySelect && citySelect.value) {
                populateDistricts(citySelect.value);
            }
        });
    </script>
</body>
</html>