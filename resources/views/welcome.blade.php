<!DOCTYPE html>
<html lang="{{ session('locale', app()->getLocale()) }}" dir="rtl">
<head>
    <!-- ================================================================= -->
    <!-- [قسم HEAD]: الإعدادات الأساسية، عناوين الصفحة، الخطوط، و Tailwind -->
    <!-- ================================================================= -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- عنوان الصفحة المتغير حسب اللغة المستعملة (كردية / عربية) -->
    <title>{{ session('locale', app()->getLocale()) == 'ku' ? 'پلاتفۆڕما عەقاراتێن من' : 'منصة عقاراتي - البوابة العقارية الذكية' }}</title>

    <!-- استدعاء خط Vazirmatn الموحد لضمان التناسق التام بين العربية والكردية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- مكتبة Tailwind CSS للتنسيق المباشر السريع -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // إعدادات الألوان المخصصة (درجات الأصفر/الذهبي العقاري) وتعيين الخط الرئيسي
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Vazirmatn', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            200: '#fef08a',
                            300: '#fde047',
                            400: '#facc15',
                            500: '#eab308',
                            600: '#ca8a04',
                            700: '#a16207',
                            800: '#854d0e',
                            900: '#713f12',
                            950: '#422006',
                        }
                    }
                }
            }
        }
    </script>

    <!-- تنسيق إضافي لفرض الخط الموحد على جميع عناصر DOM لتجنب التفاوت -->
    <style>
        * {
            font-family: 'Vazirmatn', sans-serif !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brand-500 selection:text-slate-900">

    <!-- ================================================================= -->
    <!-- [معالجة لارافيل المسبقة]: تحديد لغة الجلسة وتحديد توجيه لوحة التحكم -->
    <!-- ================================================================= -->
    @php
        // جلب رمز اللغة الحالية من الجلسة أو الإعداد الافتراضي
        $currentLang = session('locale', app()->getLocale());
        
        // جلب بيانات المستخدم المسجل للتحقق من الصلاحيات والمسارات
        $authUser = auth()->user();
        $userRole = strtolower(trim($authUser->role ?? ''));

        // توجيه تلقائي وذكي لزر لوحة التحكم حسب نوع حساب المستخدم لتجنب خطأ 403
        $dashboardRoute = url('/dashboard');
        if (str_contains($userRole, 'admin')) {
            $dashboardRoute = url('/admin/lookups'); // أو url('/admin/offices') حسب رابط صفحة الأدمن لديك
        } elseif ($userRole === 'office') {
            $dashboardRoute = url('/office/dashboard'); // لوحة تحكم المكتب العقاري
        }
    @endphp

    <!-- ================================================================= -->
    <!-- [1. قسم شريط الملاحة - NAVBAR]: الشعار، تبديل اللغة، وأزرار الحساب -->
    <!-- ================================================================= -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <!-- بلوك الشعار والاسم -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-amber-300 flex items-center justify-center text-slate-950 text-2xl shadow-lg shadow-brand-500/20 font-black">
                        🏢
                    </div>
                    <div>
                        <span class="text-xl font-black text-slate-900 tracking-tight block">
                            {{ $currentLang == 'ku' ? 'پلاتفۆڕما عەقاراتێن من' : 'منصة عقاراتي' }}
                        </span>
                        <span class="text-[10px] text-brand-700 font-bold tracking-widest uppercase">
                            {{ $currentLang == 'ku' ? 'بەرپەرێ فەرمی یێ عەقاراتان' : 'البوابة العقارية الذكية' }}
                        </span>
                    </div>
                </div>

                <!-- بلوك الأدوات: أزرار اللغة والتسجيل -->
                <div class="flex items-center gap-3">
                    
                    <!-- أزرار تبديل اللغة (الكردية والعربية) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold">
                        <a href="{{ route('lang.switch', 'ku') }}" 
                           class="px-3 py-1.5 rounded-lg transition-all {{ $currentLang == 'ku' ? 'bg-white text-brand-800 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                            کوردی
                        </a>
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="px-3 py-1.5 rounded-lg transition-all {{ $currentLang == 'ar' ? 'bg-white text-brand-800 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                            العربية
                        </a>
                    </div>

                    <!-- الأزرار الديناميكية (تتغير تلقائياً حسب حالة تسجيل الدخول) -->
                    @auth
                        <!-- يظهر فقط إذا كان المستخدم مسجلاً لدخوله ويوجّهه للوحة المناسبة لدوره -->
                        <a href="{{ $dashboardRoute }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition-all">
                            {{ $currentLang == 'ku' ? 'لوحا کۆنترۆلێ' : 'لوحة التحكم' }}
                        </a>
                    @else
                        <!-- يظهر للزوار غير المسجلين -->
                        <a href="{{ route('login') }}" class="text-slate-700 hover:text-brand-700 font-bold text-xs px-4 py-2.5 transition-all">
                            {{ $currentLang == 'ku' ? 'چوونە ژوور' : 'تسجيل الدخول' }}
                        </a>
                        
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-brand-500 via-amber-500 to-amber-600 hover:from-brand-600 hover:to-amber-700 text-slate-950 font-extrabold text-xs px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition-all">
                            ✨ {{ $currentLang == 'ku' ? 'تۆمارکرنا نوسینگەیێ' : 'تسجيل مكتب عقاري' }}
                        </a>
                    @endauth

                </div>

            </div>
        </div>
    </nav>

    <!-- ================================================================= -->
    <!-- [2. قسم الهيرو - HERO SECTION]: العناوين البارزة وبطاقات المحافظات -->
    <!-- ================================================================= -->
    <section class="relative bg-slate-950 text-white pt-20 pb-32 overflow-hidden border-b border-slate-800">
        <!-- التأثيرات الضوئية في الخلفية -->
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-brand-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <!-- شارة ترحيبية أو تعريفية متألقة -->
            <div class="inline-flex items-center gap-2 bg-brand-500/10 border border-brand-500/30 px-4 py-1.5 rounded-full text-brand-400 text-xs font-bold mb-6 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                <span>
                    {{ $currentLang == 'ku' ? 'ئێکەمین پلاتفۆڕما زیرەک بۆ عەقاراتێن هەرێمێ' : 'المنصة الذكية الأولى للعقارات في إقليم كوردستان' }}
                </span>
            </div>

            <!-- العنوان العريض والتوضيحي -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight max-w-4xl mx-auto mb-6">
                {{ $currentLang == 'ku' ? 'عەقارێ خۆ ب ئاسانی ببینە یان داخوازیا خۆ تۆمار بکە' : 'اعثر على عقارك المثالي أو قدم طلبك مباشرة للمكاتب' }}
            </h1>

            <p class="text-slate-400 text-sm sm:text-base max-w-2xl mx-auto mb-12 font-medium leading-relaxed">
                {{ $currentLang == 'ku' ? 'پارێزگەها خۆ هەڵبژێرە دا کو ب شێوەیەکێ ڕاستەوخۆ عەقاراتان ببینی یان پەیوەندیێ ب نوسینگەیێن باوەرپێکری بکەی.' : 'اختر المحافظة للبدء في تقديم طلب عقارك الخاص أو تصفح العروض الحصرية المتاحة مباشرة من المكاتب المعتمدة.' }}
            </p>

            <!-- ========================================================= -->
            <!-- [3. شبكة بطاقات المحافظات المتجاوبة - Responsive Grid System]: -->
            <!-- تحسين التجاوب عبر شاشات الموبايل (1 عمود) إلى الشاشات المتوسطة والكبيرة (3 أعمدة) -->
            <!-- ========================================================= -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto text-right">
                
                <!-- بطاقة محافظة دهوك -->
                <a href="{{ route('property.request.page', ['city' => 'duhok']) }}" 
                   class="group relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl hover:border-brand-500 transition-all duration-300 hover:-translate-y-2 hover:shadow-brand-500/10">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80" 
                             alt="Duhok Real Estate" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md text-brand-400 text-xs px-3 py-1 rounded-full border border-brand-500/30 font-bold">
                            📍 {{ $currentLang == 'ku' ? 'دهۆک' : 'دهوك' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">
                            {{ $currentLang == 'ku' ? 'عەقاراتێن پارێزگەها دهۆک' : 'عقارات محافظة دهوك' }}
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            {{ $currentLang == 'ku' ? 'داواکرن یان گەڕیان ل دووڤ خانوو، شوقە و عەردان ل پارێزگەها دهۆکێ' : 'استعرض أحدث العروض والطلبات المباشرة في كافة مناطق ونواحي دهوك' }}
                        </p>
                        <div class="flex items-center text-xs text-brand-400 font-bold gap-1 group-hover:gap-2 transition-all">
                            <span>{{ $currentLang == 'ku' ? 'دەستپێبکە' : 'انتقل الآن' }}</span>
                            <span class="text-base">←</span>
                        </div>
                    </div>
                </a>

                <!-- بطاقة محافظة أربيل -->
                <a href="{{ route('property.request.page', ['city' => 'erbil']) }}" 
                   class="group relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl hover:border-brand-500 transition-all duration-300 hover:-translate-y-2 hover:shadow-brand-500/10">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80" 
                             alt="Erbil Real Estate" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md text-brand-400 text-xs px-3 py-1 rounded-full border border-brand-500/30 font-bold">
                            📍 {{ $currentLang == 'ku' ? 'هەولێر' : 'أربيل' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">
                            {{ $currentLang == 'ku' ? 'عەقاراتێن پارێزگەها هەولێر' : 'عقارات محافظة أربيل' }}
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            {{ $currentLang == 'ku' ? 'کڕین و فرۆتنا ئاڤاهی، ڤێلا و شوقەیان ل پایتەختێ هەرێمێ' : 'تصفح أفضل الفرص العقارية والمجمعات السكنية في العاصمة أربيل' }}
                        </p>
                        <div class="flex items-center text-xs text-brand-400 font-bold gap-1 group-hover:gap-2 transition-all">
                            <span>{{ $currentLang == 'ku' ? 'دەستپێبکە' : 'انتقل الآن' }}</span>
                            <span class="text-base">←</span>
                        </div>
                    </div>
                </a>

                <!-- بطاقة محافظة السليمانية -->
                <a href="{{ route('property.request.page', ['city' => 'sulaymaniyah']) }}" 
                   class="group relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl hover:border-brand-500 transition-all duration-300 hover:-translate-y-2 hover:shadow-brand-500/10">
                    <div class="h-44 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80" 
                             alt="Sulaymaniyah Real Estate" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-90 group-hover:opacity-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                        <span class="absolute top-3 right-3 bg-slate-950/80 backdrop-blur-md text-brand-400 text-xs px-3 py-1 rounded-full border border-brand-500/30 font-bold">
                            📍 {{ $currentLang == 'ku' ? 'سلێمانی' : 'السليمانية' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">
                            {{ $currentLang == 'ku' ? 'عەقاراتێن پارێزگەها سلێمانیێ' : 'عقارات محافظة السليمانية' }}
                        </h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            {{ $currentLang == 'ku' ? 'دیتنا باشترین عەقارات و پروژەیێن ئاڤەدانکرنێ ل سلێمانیێ' : 'الوصول المباشر إلى المكاتب المعتمدة والخيارات المتاحة بالسليمانية' }}
                        </p>
                        <div class="flex items-center text-xs text-brand-400 font-bold gap-1 group-hover:gap-2 transition-all">
                            <span>{{ $currentLang == 'ku' ? 'دەستپێبکە' : 'انتقل الآن' }}</span>
                            <span class="text-base">←</span>
                        </div>
                    </div>
                </a>

            </div>

        </div>
    </section>

    <!-- ================================================================= -->
    <!-- [4. قسم ميزات المنصة - FEATURES]: المزايا الأساسية والخدمات المقدمة -->
    <!-- ================================================================= -->
    <section class="py-20 bg-slate-50 relative -mt-10 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- ميزة 1: نظام المطابقة -->
                <div class="bg-white p-8 rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-100 text-center hover:border-brand-500/40 transition-all">
                    <div class="w-16 h-16 mx-auto bg-amber-50 text-brand-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner border border-amber-100">
                        🎯
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-3">
                        {{ $currentLang == 'ku' ? 'گونجاندنا دروست' : 'مطابقة دقيقة' }}
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        {{ $currentLang == 'ku' ? 'سیستەمێ مە ب دروستی داواکاریا تە ب عەقاراتێن بەردەست ڤە گرێددەت.' : 'نظام مطابقة فوري يربط طلبك بالعقارات المتوفرة لدى مكاتب منطقتك بدقة عالية.' }}
                    </p>
                </div>

                <!-- ميزة 2: التواصل المباشر -->
                <div class="bg-white p-8 rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-100 text-center hover:border-brand-500/40 transition-all">
                    <div class="w-16 h-16 mx-auto bg-amber-50 text-brand-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner border border-amber-100">
                        💬
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-3">
                        {{ $currentLang == 'ku' ? 'پەیوەندیا ڕاستەوخۆ' : 'تواصل مباشر' }}
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        {{ $currentLang == 'ku' ? 'پەیوەندیا ڕاستەوخۆ ب ڕێکا واتساپێ یان تەلەفۆنێ بێی ناڤبەرکار.' : 'مراسلة مباشرة مع أصحاب المكاتب المعتمدين عبر الواتساب أو الهاتف بدون وسيط.' }}
                    </p>
                </div>

                <!-- ميزة 3: مكاتب معتمدة -->
                <div class="bg-white p-8 rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-100 text-center hover:border-brand-500/40 transition-all">
                    <div class="w-16 h-16 mx-auto bg-amber-50 text-brand-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner border border-amber-100">
                        🏢
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-3">
                        {{ $currentLang == 'ku' ? 'نوسینگەیێن باوەرپێکری' : 'مكاتب معتمدة' }}
                    </h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        {{ $currentLang == 'ku' ? 'هەموو عەقارات ژ لایێ نوسینگەیێن فەرمی ئێن هەرێمێ دهێنە بەلاڤکرن.' : 'عقارات موثوقة مضافة مباشرة من مكاتب عقارية ترخيصها رسمي في الإقليم.' }}
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- ================================================================= -->
    <!-- [5. تذييل الصفحة - FOOTER]: حقوق النشر وروابط التنقل التكميلية -->
    <!-- ================================================================= -->
    @include('layouts.footer')

</body>
</html>