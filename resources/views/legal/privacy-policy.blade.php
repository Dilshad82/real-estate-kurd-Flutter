{{-- 
  ==========================================================================
  ملف صفحة سياسة الخصوصية (privacy-policy.blade.php)
  --------------------------------------------------------------------------
  الوظيفة الأساسية:
  1. عرض بنود وسياسات الخصوصية الخاصة بالمنصة وتطبيقات المتاجر.
  2. توفير التوافق التام مع اللغتين العربية والكردية (RTL & LTR Compatible).
  3. استيفاء الشروط الإلزامية لمنصات Google Play Console و Apple App Store.
  ==========================================================================
--}}

<x-guest-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-10 space-y-8">
                
                {{-- العنوان الرئيسي لصفحة الخصوصية --}}
                <div class="border-b border-slate-100 pb-6 text-center rtl:text-right ltr:text-left">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800">
                        {{ __('site.privacy_policy_title') }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-2">
                        {{ __('site.last_updated') }} {{ date('Y-m-d') }}
                    </p>
                </div>

                {{-- الملاحظة والمقدمة التعريفية --}}
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    {{ __('site.privacy_intro') }}
                </p>

                {{-- بند (1): جمع المعلومات الشخصية --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.info_collection') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.info_collection_desc') }}
                    </p>
                </div>

                {{-- بند (2): بيانات الموقع الجغرافي --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.location_data') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.location_data_desc') }}
                    </p>
                </div>

                {{-- بند (3): آلية استخدام وتوظيف البيانات --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.data_usage') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.data_usage_desc') }}
                    </p>
                </div>

                {{-- بند (4): أمان الحسابات والتشفير --}}
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ __('site.data_security') }}
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed ps-4">
                        {{ __('site.data_security_desc') }}
                    </p>
                </div>

                {{-- زر العودة للصفحة الرئيسية --}}
                <div class="pt-6 border-t border-slate-100 flex justify-center">
                    <a href="{{ url('/') }}" class="px-6 py-2.5 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-black transition-all">
                        {{ __('site.dashboard') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>