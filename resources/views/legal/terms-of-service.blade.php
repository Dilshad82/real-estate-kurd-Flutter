{{-- 
  ==========================================================================
  ملف صفحة شروط الاستخدام (terms-of-service.blade.php)
  --------------------------------------------------------------------------
  الوظيفة الأساسية:
  1. عرض الشروط والأحكام الخاصة باستعمال المنصة والتطبيق الذكي.
  2. تنظيم العلاقة بين المكاتب العقارية، الزوار، وإدارة المنصة.
  3. استيفاء متطلبات المتاجر الرسمية والتوافق التام مع اللغتين العربية والكردية.
  ==========================================================================
--}}

<x-guest-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-10 space-y-8">
                
                {{-- العنوان الرئيسي للشروط والأحكام --}}
                <div class="border-b border-slate-100 pb-6 text-center rtl:text-right ltr:text-left">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800">
                        {{ __('site.terms_service_title') }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-2">
                        {{ __('site.last_updated') }} {{ date('Y-m-d') }}
                    </p>
                </div>

                {{-- مقدمة اتفاقية الاستخدام --}}
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    {{ __('site.terms_intro') }}
                </p>

                {{-- البند الأول: حسابات المستخدمين والمكاتب --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.user_accounts') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.user_accounts_desc') }}
                    </p>
                </div>

                {{-- البند الثاني: ضوابط إعلانات العقارات --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.property_listings') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.property_listings_desc') }}
                    </p>
                </div>

                {{-- البند الثالث: إخلاء المسؤولية والتنظيم القانوني --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.limitation_liability') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.limitation_liability_desc') }}
                    </p>
                </div>

                {{-- زر العودة للرئيسية --}}
                <div class="pt-6 border-t border-slate-100 flex justify-center">
                    <a href="{{ url('/') }}" class="px-6 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-black transition-all">
                        {{ __('site.dashboard') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>