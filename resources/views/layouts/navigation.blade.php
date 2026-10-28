{{-- 
    ====================================================================================
    ملف شريط التنقل الرئيسي والملاحة العلوي (resources/views/layouts/navigation.blade.php)
    ====================================================================================
--}}

<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50 transition-all duration-300">
    
    <!-- ================================================================== -->
    <!-- القائمة المخصصة للشاشات الكبيرة والمكتبية (Desktop Navigation)       -->
    <!-- ================================================================== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <x-responsive-nav-link :href="url('/')" :active="request()->is('/')" class="rounded-xl font-semibold">
                        {{ app()->getLocale() == 'ku' ? 'سەرەکی' : 'الرئيسية' }}
                </x-responsive-nav-link>
                {{-- أ) شعار التطبيق (Application Logo) --}}
                <div class="shrink-0 flex items-center">
                    @php
                        $user = Auth::user();
                        $dashboardRoute = route('login'); // المسار الافتراضي للزائر غير المسجل

                        if ($user) {
                            $isAdmin = !empty($user->is_admin) || $user->role === 'admin' || str_starts_with($user->role ?? '', 'admin_');
                            $dashboardRoute = $isAdmin ? route('admin.lookups') : route('office.dashboard');
                        }
                    @endphp
                    
                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 group transition-transform duration-200 hover:scale-105">
                        <div class="p-2 rounded-xl bg-indigo-600/10 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <x-application-logo class="block h-7 w-auto fill-current" />
                        </div>
                    </a>
                </div>

                {{-- ب) روابط القائمة الرئيسية للشاشات الكبيرة --}}
                <div class="hidden space-x-6 space-x-reverse sm:-my-px sm:ms-6 sm:flex items-center">
                    
                    @php
                        $authUser = Auth::user();
                        $userRole = strtolower(trim($authUser->role ?? ''));
                        $isAdminUser = $authUser && (!empty($authUser->is_admin) || $userRole === 'admin' || str_starts_with($userRole, 'admin_'));
                        $isSuperAdmin = $authUser && (!empty($authUser->is_admin) || $userRole === 'admin');
                    @endphp

                    {{-- 1. إدارة المحافظات والمناطق --}}
                    @if($isAdminUser)
                        <x-nav-link :href="route('admin.lookups')" :active="request()->routeIs('admin.lookups*')" class="text-sm font-bold transition-all text-indigo-900">
                            <svg class="w-4 h-4 ms-1.5 opacity-70 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('site.manage_cities_districts') }}
                        </x-nav-link>
                    @endif

                    {{-- 2. إدارة المكاتب العقارية (للمدير العام Super Admin) --}}
                    @if($isSuperAdmin)
                        <x-nav-link :href="route('admin.offices.index')" :active="request()->routeIs('admin.offices*')" class="text-sm font-bold transition-all text-indigo-900">
                            <svg class="w-4 h-4 ms-1.5 opacity-70 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ __('site.manage_offices') }}
                        </x-nav-link>
                    @endif

                    {{-- 3. مكتبي العقاري --}}
                    @auth
                        <x-nav-link :href="route('office.dashboard')" :active="request()->routeIs('office.dashboard*')" class="text-sm font-semibold transition-all">
                            <svg class="w-4 h-4 ms-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            {{ __('site.my_office') }}
                        </x-nav-link>
                    @endauth

                </div>
            </div>

            <!-- ================================================================== -->
            <!-- 2. الجزء الأيسر: زر التبديل بين اللغات والقائمة المنسدلة للمستخدم    -->
            <!-- ================================================================== -->
            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                
                {{-- أ) زر التبديل بين اللغات --}}
                <div class="flex items-center">
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'ku') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100/80 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl border border-slate-200/80 transition-all shadow-xs">
                            <span>🌐</span>
                            <span>کوردی (Badinî)</span>
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100/80 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl border border-slate-200/80 transition-all shadow-xs">
                            <span>🌐</span>
                            <span>العربية</span>
                        </a>
                    @endif
                </div>

                {{-- ب) القائمة المنسدلة لإعدادات حساب المستخدم --}}
                @auth
                    {{-- يظهر للمستخدم المسجل دخوله فقط --}}
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 px-3.5 py-1.5 border border-slate-200/80 rounded-xl text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-xs text-slate-800">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ app()->getLocale() == 'ku' ? 'پرۆفایل' : 'الملف الشخصي' }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ app()->getLocale() == 'ku' ? 'دەرکەوتن' : 'تسجيل الخروج' }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- يظهر للزائرين غير المسجلين فقط --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition">
                            {{ app()->getLocale() == 'ku' ? 'چوونەژوورەوە' : 'تسجيل الدخول' }}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">
                            {{ app()->getLocale() == 'ku' ? 'تۆمارکرن' : 'إنشاء حساب' }}
                        </a>
                    </div>
                @endauth    
            </div>

            <!-- ================================================================== -->
            <!-- 3. زر الهامبرغر لشاشات الموبايل (Mobile Hamburger)                   -->
            <!-- ================================================================== -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- 4. القائمة التفاعلية لشاشات الموبايل (Mobile Drawer Menu)             -->
    <!-- ================================================================== -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md">
        
        <div class="pt-2 pb-3 space-y-1 px-2">
            
            <div class="px-3 py-2 border-b border-slate-100 mb-2">
                @if(app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'ku') }}" 
                       class="flex items-center justify-center gap-2 w-full py-2 px-3 text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">
                        <span>🌐</span>
                        <span>کوردی (Badinî)</span>
                    </a>
                @else
                    <a href="{{ route('lang.switch', 'ar') }}" 
                       class="flex items-center justify-center gap-2 w-full py-2 px-3 text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">
                        <span>🌐</span>
                        <span>العربية</span>
                    </a>
                @endif
            </div>

            @if($isAdminUser)
                <x-responsive-nav-link :href="route('admin.lookups')" :active="request()->routeIs('admin.lookups*')" class="rounded-xl font-semibold">
                    {{ __('site.manage_cities_districts') }}
                </x-responsive-nav-link>
            @endif

            @if($isSuperAdmin)
                <x-responsive-nav-link :href="route('admin.offices.index')" :active="request()->routeIs('admin.offices*')" class="rounded-xl font-semibold">
                    {{ __('site.manage_offices') }}
                </x-responsive-nav-link>
            @endif

            @auth
                <x-responsive-nav-link :href="route('office.dashboard')" :active="request()->routeIs('office.dashboard*')" class="rounded-xl font-semibold">
                    {{ __('site.my_office') }}
                </x-responsive-nav-link>
            @endauth

        </div>

        @auth
            {{-- تعرض تفاصيل الحساب وزر الخروج بالموبايل للمسجلين فقط --}}
            <div class="pt-4 pb-3 border-t border-slate-100 bg-slate-50/50">
                <div class="px-4 flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-800">
                            {{ Auth::user()->name === 'مدير النظام' ? __('site.system_admin') : Auth::user()->name }}
                        </div>
                        <div class="font-medium text-xs text-slate-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1 px-2">
                    <x-responsive-nav-link :href="route('profile.edit')" class="rounded-xl text-xs font-semibold">
                        {{ __('site.profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                class="rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50/80"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('site.logout') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            {{-- أزرار الدخول والتسجيل بالموبايل للزوار --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center py-2 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition">
                    {{ app()->getLocale() == 'ku' ? 'چوونەژوورەوە' : 'تسجيل الدخول' }}
                </a>
                <a href="{{ route('register') }}" class="w-full text-center py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">
                    {{ app()->getLocale() == 'ku' ? 'تۆمارکرن' : 'إنشاء حساب' }}
                </a>
            </div>
        @endauth
    </div>
</nav>