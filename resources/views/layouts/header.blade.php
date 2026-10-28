<header class="bg-white/90 backdrop-blur-md shadow-xs border-b border-slate-200/80 py-3 px-4 sm:px-6 mb-6 sticky top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto flex justify-between items-center gap-4">
        
        <!-- عنوان وشعار المنصة -->
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-base sm:text-lg font-black text-slate-800 hover:text-indigo-600 transition-colors">
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 text-base">🏢</span>
                <span>{{ __('site.title') ?? (app()->getLocale() == 'ku' ? 'خانوبەرێن هەرێما کوردستانێ' : 'عقارات إقليم كردستان') }}</span>
            </a>
        </div>

        <!-- القائمة والتحكم الأيسر -->
        <div class="flex items-center gap-3">
            
            <!-- أزرار تبديل اللغة المحدثة بنظام اللمسات العصرية -->
            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                <a href="{{ route('lang.switch', 'ar') }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ app()->getLocale() == 'ar' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    العربية
                </a>
                <a href="{{ route('lang.switch', 'ku') }}" 
                   class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ app()->getLocale() == 'ku' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Kurdî (کوردی)
                </a>
            </div>

            <!-- معلومات المستخدم وزر الخروج في لوحات التحكم -->
            @auth
                @if(request()->is('admin*') || request()->is('office*'))
                    <div class="border-r border-slate-200 pr-3 mr-1 flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-bold hidden md:inline bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/60">
                            👤 {{ auth()->user()->name }}
                        </span>
                        
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                <span>{{ app()->getLocale() == 'ku' ? 'دەرکەوتن' : 'تسجيل الخروج' }}</span>
                            </button>
                        </form>
                    </div>
                @endif
            @endauth

        </div>

    </div>
</header>