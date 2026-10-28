<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs rounded-xl transition border border-rose-200/60 cursor-pointer">
                        @if(app()->getLocale() == 'ku') دەرکەوتن @else تسجيل الخروج @endif
                    </button>
                </form>

                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                    <a href="{{ route('lang.switch', 'ku') }}" class="px-3 py-1 {{ app()->getLocale() == 'ku' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900' }} text-xs rounded-lg transition-all">
                        Kurdî (کوردی)
                    </a>
                    <a href="{{ route('lang.switch', 'ar') }}" class="px-3 py-1 {{ app()->getLocale() == 'ar' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900' }} text-xs rounded-lg transition-all">
                        العربية
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.offices.index') }}" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200 flex items-center gap-1.5">
                        <i class="fas fa-building text-slate-400"></i>
                        <span>@if(app()->getLocale() == 'ku') نوسینگەی خانوبەرەکان @else المكاتب العقارية @endif</span>
                    </a>
                @endif

                <h1 class="text-sm md:text-base font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-lg">🏢</span>
                    <span>{{ __('site.title') }}</span>
                </h1>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        editCityModal: false, editCityData: { id: null, name: '' },
        editDistrictModal: false, editDistrictData: { id: null, name: '' },
        editTypeModal: false, editTypeData: { id: null, name: '' },
        editAdminModal: false, editAdminData: { id: null, name: '', email: '', role: 'admin' }
    }" class="py-8 bg-slate-50 min-h-screen" dir="rtl">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                    {{ __('site.dashboard_title') }}
                </h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full mt-2"></div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl font-medium text-sm flex justify-between items-center shadow-xs">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg px-2 cursor-pointer">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200/80 text-rose-700 rounded-2xl text-sm shadow-xs">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i>
                        <span>@if(app()->getLocale() == 'ku') تکایە هەڵەکان چاکبکەوە: @else يرجى تصحيح الأخطاء التالية: @endif</span>
                    </div>
                    <ul class="list-disc pr-5 space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(auth()->check() && auth()->user()->role === 'admin')
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 md:p-8 mb-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 left-0 h-1 bg-indigo-600"></div>
                <h3 class="font-bold text-slate-900 text-base mb-6 flex items-center gap-2">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl"><i class="fas fa-user-shield"></i></span>
                    <span>@if(app()->getLocale() == 'ku') زیادکردنی ئەدمینی نوێ بۆ سیستم یان پارێزگاکان @else إضافة أدمن جديد للنظام أو المحافظات @endif</span>
                </h3>

                <form action="{{ route('admin.city_admins.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            @if(app()->getLocale() == 'ku') ناو @else الاسم الكامل @endif
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            @if(app()->getLocale() == 'ku') ئیمێڵ @else البريد الإلكتروني @endif
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800 text-right dir-ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            @if(app()->getLocale() == 'ku') وشەی نهێنی @else كلمة المرور @endif
                        </label>
                        <input type="password" name="password" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800 text-right dir-ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            @if(app()->getLocale() == 'ku') صلاحية الأدمن / المحافظة @else الصلاحية / المحافظة المسندة @endif
                        </label>
                        <select name="city_role" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 font-bold text-slate-800 cursor-pointer">
                            <option value="admin">👑 @if(app()->getLocale() == 'ku') ئەدمینی گشتی (سەرەکی) @else أدمن عام (مدير النظام) @endif</option>
                            @foreach($cities as $city)
                                @php
                                    $cityName = is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name;
                                @endphp
                                <option value="admin_{{ $city->id }}">
                                    🏢 @if(app()->getLocale() == 'ku') ئەدمینی پارێزگای {{ $cityName }} @else أدمن محافظة {{ $cityName }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 flex justify-end pt-2">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-plus text-xs"></i>
                            <span>@if(app()->getLocale() == 'ku') تۆمارکردنی ئەدمین @else حفظ وإنشاء الأدمن @endif</span>
                        </button>
                    </div>
                </form>

                @if(isset($cityAdmins) && $cityAdmins->count() > 0)
                    <div class="border-t border-slate-100 pt-5 mt-2">
                        <h4 class="text-xs font-bold text-slate-500 mb-3 uppercase tracking-wider">
                            @if(app()->getLocale() == 'ku') لیستی ئەدمینەکانی ئێستا: @else قائمة المدراء والأدمن الحاليين: @endif
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($cityAdmins as $adminUser)
                                @php
                                    $roleValue = strtolower(trim($adminUser->role));

                                    // 1. فحص الترجمة مباشرة من ملف lang/site.php
                                    if (\Illuminate\Support\Facades\Lang::has('site.' . $roleValue)) {
                                        $assignedCityName = __('site.' . $roleValue);
                                    } elseif (str_starts_with($roleValue, 'admin_')) {
                                        $cityId = str_replace('admin_', '', $roleValue);
                                        $assignedCity = $cities->firstWhere('id', $cityId);

                                        if ($assignedCity) {
                                            $assignedCityName = is_array($assignedCity->name) 
                                                ? ($assignedCity->name[app()->getLocale()] ?? reset($assignedCity->name)) 
                                                : $assignedCity->name;
                                        } else {
                                            $assignedCityName = $adminUser->role;
                                        }
                                    } else {
                                        $assignedCityName = $roleValue === 'admin' 
                                            ? __('site.admin_general') 
                                            : $adminUser->role;
                                    }

                                    // 2. ترجمة اسم المستخدم إذا كان مخزناً بـ "مدير النظام"
                                    $displayName = $adminUser->name;
                                    if (($adminUser->name === 'مدير النظام' || $adminUser->name === 'admin') && app()->getLocale() == 'ku') {
                                        $displayName = 'ڕێڤەبەرێ سیستەمی';
                                    }
                                @endphp

                                <div class="flex justify-between items-center p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs hover:border-indigo-200 transition">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $displayName }}</p>
                                        <p class="text-slate-500 text-[11px] dir-ltr text-right">{{ $adminUser->email }}</p>
                                        
                                        <span class="inline-block mt-1.5 px-2.5 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200/60 rounded-full font-bold text-[10px]">
                                            📍 {{ $assignedCityName }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col gap-1.5 text-right">
                                        <button type="button" 
                                            @click="editAdminModal = true; editAdminData = { id: {{ $adminUser->id }}, name: @js($adminUser->name), email: @js($adminUser->email), role: @js($adminUser->role) }" 
                                            class="text-indigo-600 hover:text-indigo-800 font-bold transition text-xs cursor-pointer">
                                            {{ app()->getLocale() == 'ku' ? 'دەستکاری' : 'تعديل' }}
                                        </button>

                                        <form action="{{ route('admin.city_admins.destroy', $adminUser) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('{{ app()->getLocale() == 'ku' ? 'تۆ دڵنیایی؟' : 'هل أنت متأكد من سحب الصلاحيات؟' }}')" class="text-rose-500 hover:text-rose-700 font-bold transition text-xs cursor-pointer">
                                                {{ app()->getLocale() == 'ku' ? 'سڕینەوە' : 'سحب الصلاحية' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 {{ auth()->check() && auth()->user()->role === 'admin' ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-6 mb-8"> 
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 flex flex-col justify-between hover:border-indigo-200 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl text-sm"><i class="fas fa-map-marker-alt"></i></span>
                            <h3 class="font-bold text-slate-900 text-base">
                                {{ __('site.add_district') }} +
                            </h3>
                        </div>
                        
                        <form id="districtForm" action="{{ route('admin.districts.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.city') }}</label>
                                <select name="city_id" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 font-semibold text-slate-800 cursor-pointer">
                                    <option value="" disabled selected>{{ __('site.select_city') }}</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">
                                            {{ is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.district') }}</label>
                                <input type="text" name="name" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                            </div>
                        </form>
                    </div>

                    <div class="pt-6">
                        <button type="submit" form="districtForm" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            {{ __('site.save') }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 flex flex-col justify-between hover:border-indigo-200 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl text-sm"><i class="fas fa-home"></i></span>
                            <h3 class="font-bold text-slate-900 text-base">
                                {{ __('site.add_type') }} +
                            </h3>
                        </div>
                        
                        <form id="typeForm" action="{{ route('admin.types.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.property_type') }}</label>
                                <input type="text" name="name" required class="w-full text-xs border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                            </div>
                        </form>
                    </div>

                    <div class="pt-6">
                        <button type="submit" form="typeForm" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            {{ __('site.save') }}
                        </button>
                    </div>
                </div>

                @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 flex flex-col justify-between hover:border-indigo-200 transition">
                    <div>
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                            <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm"><i class="fas fa-city"></i></span>
                            <h3 class="font-bold text-slate-900 text-base">
                                {{ __('site.add_city') }} +
                            </h3>
                        </div>
                        
                        <form id="cityForm" action="{{ route('admin.cities.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.city') }}</label>
                                <input type="text" name="name" required class="w-full text-xs border-slate-200 rounded-xl focus:border-emerald-600 focus:ring-emerald-500/20 p-3 bg-slate-50 text-slate-800">
                            </div>
                        </form>
                    </div>

                    <div class="pt-6">
                        <button type="submit" form="cityForm" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            {{ __('site.save') }}
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 md:p-8">
                    <div class="text-center mb-6">
                        <h3 class="font-extrabold text-slate-900 text-lg">
                            {{ __('site.city') }} / {{ __('site.district') }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">عرض وتعديل المحافظات والمناطق المسجلة بالنظام</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($cities as $city)
                            @php
                                $cityName = is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name;
                            @endphp
                            <div class="border border-slate-200/80 rounded-2xl p-4 bg-slate-50/50 hover:border-indigo-200 transition">
                                <div class="flex justify-between items-center mb-3 pb-2.5 border-b border-slate-200/60">
                                    <h4 class="font-extrabold text-indigo-900 text-sm flex items-center gap-1.5">
                                        <i class="fas fa-map-pin text-indigo-500 text-xs"></i>
                                        <span>{{ $cityName }}</span>
                                    </h4>
                                    
                                    <div class="flex items-center gap-2 text-xs">
                                        <button type="button" 
                                            @click="editCityModal = true; editCityData = { id: {{ $city->id }}, name: @js($cityName) }" 
                                            class="text-indigo-600 hover:text-indigo-800 font-bold transition cursor-pointer">
                                            @if(app()->getLocale() == 'ku') دەستکاری @else تعديل @endif
                                        </button>

                                        <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('@if(app()->getLocale() == 'ku') تۆ دڵنیایی؟ @else هل أنت متأكد من حذف المحافظة؟ @endif')" class="text-rose-500 hover:text-rose-700 font-bold transition cursor-pointer">
                                                @if(app()->getLocale() == 'ku') سڕینەوە @else حذف @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <ul class="space-y-2">
                                    @forelse($city->districts ?? [] as $district)
                                        @php
                                            $districtName = is_array($district->name) ? ($district->name[app()->getLocale()] ?? reset($district->name)) : $district->name;
                                        @endphp
                                        <li class="flex justify-between items-center text-xs bg-white p-2.5 rounded-xl border border-slate-100 shadow-2xs">
                                            <span class="text-slate-700 font-bold text-xs">{{ $districtName }}</span>
                                            
                                            <div class="flex gap-2 text-[11px]">
                                                <button type="button" 
                                                    @click="editDistrictModal = true; editDistrictData = { id: {{ $district->id }}, name: @js($districtName) }" 
                                                    class="text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer">
                                                    @if(app()->getLocale() == 'ku') دەستکاری @else تعديل @endif
                                                </button>
                                                
                                                <form action="{{ route('admin.districts.destroy', $district) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('@if(app()->getLocale() == 'ku') تۆ دڵنیایی؟ @else هل أنت متأكد من حذف المنطقة؟ @endif')" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">
                                                        @if(app()->getLocale() == 'ku') سڕینەوە @else حذف @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-xs text-slate-400 text-center py-2 font-medium">
                                            @if(app()->getLocale() == 'ku') هیچ ناوچەیەک نییە @else لا توجد مناطق مضافة @endif
                                        </li>
                                    @endforelse
                                </ul>

                            </div>
                        @empty
                            <div class="col-span-2 text-center text-slate-400 py-8 text-xs font-medium">
                                @if(app()->getLocale() == 'ku') هیچ پارێزگایەک تۆمار نەکراوە @else لا توجد محافظات مسجلة @endif
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 md:p-8">
                    <div class="text-center mb-6">
                        <h3 class="font-extrabold text-slate-900 text-lg">
                            {{ __('site.property_type') }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">تصنيفات العقارات المتاحة</p>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($types as $type)
                            @php
                                $typeName = is_array($type->name) ? ($type->name[app()->getLocale()] ?? reset($type->name)) : $type->name;
                            @endphp
                            <div class="flex justify-between items-center p-3.5 bg-slate-50/80 hover:bg-slate-100 rounded-xl border border-slate-200/70 transition">
                                <span class="font-bold text-slate-800 text-xs">🏢 {{ $typeName }}</span>
                                
                                <div class="flex gap-2.5 text-xs">
                                    <button type="button" 
                                        @click="editTypeModal = true; editTypeData = { id: {{ $type->id }}, name: @js($typeName) }" 
                                        class="text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer">
                                        @if(app()->getLocale() == 'ku') دەستکاری @else تعديل @endif
                                    </button>
                                    
                                    <form action="{{ route('admin.types.destroy', $type) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('@if(app()->getLocale() == 'ku') تۆ دڵنیایی؟ @else هل أنت متأكد من الحذف؟ @endif')" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">
                                            @if(app()->getLocale() == 'ku') سڕینەوە @else حذف @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-slate-400 py-8 text-xs font-medium">
                                @if(app()->getLocale() == 'ku') هیچ جۆرێک تۆمار نەکراوە @else لا توجد أنواع مسجلة @endif
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <div x-show="editCityModal" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4" style="display: none;">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-slate-100" @click.away="editCityModal = false">
                <h3 class="font-bold text-slate-900 text-sm mb-4 pb-2 border-b border-slate-100">@if(app()->getLocale() == 'ku') دەستکاریکردنی پارێزگا @else تعديل المحافظة @endif</h3>
                <form :action="'{{ url('/admin/cities') }}/' + editCityData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.city') }}</label>
                        <input type="text" name="name" x-model="editCityData.name" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="editCityModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">@if(app()->getLocale() == 'ku') پاشگەزبوونەوە @else إلغاء @endif</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">{{ __('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editDistrictModal" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4" style="display: none;">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-slate-100" @click.away="editDistrictModal = false">
                <h3 class="font-bold text-slate-900 text-sm mb-4 pb-2 border-b border-slate-100">@if(app()->getLocale() == 'ku') دەستکاریکردنی ناوچە @else تعديل المنطقة @endif</h3>
                <form :action="'{{ url('/admin/districts') }}/' + editDistrictData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.district') }}</label>
                        <input type="text" name="name" x-model="editDistrictData.name" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="editDistrictModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">@if(app()->getLocale() == 'ku') پاشگەزبوونەوە @else إلغاء @endif</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">{{ __('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editTypeModal" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4" style="display: none;">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-slate-100" @click.away="editTypeModal = false">
                <h3 class="font-bold text-slate-900 text-sm mb-4 pb-2 border-b border-slate-100">@if(app()->getLocale() == 'ku') دەستکاریکردنی جۆری خانوبەرە @else تعديل نوع العقار @endif</h3>
                <form :action="'{{ url('/admin/types') }}/' + editTypeData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ __('site.property_type') }}</label>
                        <input type="text" name="name" x-model="editTypeData.name" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="editTypeModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">@if(app()->getLocale() == 'ku') پاشگەزبوونەوە @else إلغاء @endif</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">{{ __('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="editAdminModal" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4" style="display: none;">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-slate-100" @click.away="editAdminModal = false">
                <h3 class="font-bold text-slate-900 text-sm mb-4 pb-2 border-b border-slate-100">
                    @if(app()->getLocale() == 'ku') دەستکاریکردنی ئەدمین @else تعديل بيانات الأدمن @endif
                </h3>
                <form :action="'{{ url('/admin/city-admins') }}/' + editAdminData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            @if(app()->getLocale() == 'ku') ناو @else الاسم الكامل @endif
                        </label>
                        <input type="text" name="name" x-model="editAdminData.name" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            @if(app()->getLocale() == 'ku') ئیمێڵ @else البريد الإلكتروني @endif
                        </label>
                        <input type="email" name="email" x-model="editAdminData.email" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800 dir-ltr text-right">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            @if(app()->getLocale() == 'ku') وشەی نهێنی (ئارەزوومەندانە) @else كلمة المرور (اختياري) @endif
                        </label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800 dir-ltr text-right">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            @if(app()->getLocale() == 'ku') صلاحية الأدمن / المحافظة @else الصلاحية / المحافظة المسندة @endif
                        </label>
                        <select name="role" x-model="editAdminData.role" required class="w-full text-xs border border-slate-200 rounded-xl focus:border-indigo-600 focus:ring-indigo-500/20 p-3 bg-slate-50 text-slate-800 font-bold cursor-pointer">
                            <option value="admin">👑 @if(app()->getLocale() == 'ku') ئەدمینی گشتی (سەرەکی) @else أدمن عام (مدير النظام) @endif</option>
                            @foreach($cities as $city)
                                @php
                                    $cityName = is_array($city->name) ? ($city->name[app()->getLocale()] ?? reset($city->name)) : $city->name;
                                @endphp
                                <option value="admin_{{ $city->id }}">
                                    🏢 @if(app()->getLocale() == 'ku') ئەدمینی پارێزگای {{ $cityName }} @else أدمن محافظة {{ $cityName }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editAdminModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                            @if(app()->getLocale() == 'ku') پاشگەزبوونەوە @else إلغاء @endif
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                            {{ __('site.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>