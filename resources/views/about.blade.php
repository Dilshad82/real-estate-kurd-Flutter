<x-app-layout>
    {{-- حقن خط Cairo وتنسيق الاتصال المستقر للحروف الكردية --}}
    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @endpush

    <style>
        /* إجبار محرك المتصفح على توصيل الحروف للغة الكردية والعربية */
        .kurdo-font, 
        .kurdo-font * {
            font-family: 'Cairo', system-ui, -apple-system, sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
            font-feature-settings: "liga" 1, "calt" 1, "kern" 1 !important;
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased;
        }
    </style>

    {{-- عنوان الصفحة --}}
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight kurdo-font">
            {{ __('site.about_us') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen kurdo-font" dir="rtl" lang="{{ app()->getLocale() }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- البطاقة الرئيسية --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-8 md:p-12 text-center">
                <div class="inline-flex items-center justify-center p-3 bg-indigo-50 text-indigo-600 rounded-2xl mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 mb-6 leading-snug">
                    {{ __('site.about_us') }}
                </h1>
                
                <p class="text-slate-600 max-w-3xl mx-auto text-base md:text-lg leading-loose mb-6 font-medium">
                    {{ __('site.about_description_1') }}
                </p>

                <p class="text-slate-500 max-w-2xl mx-auto text-sm md:text-base leading-loose font-normal">
                    {{ __('site.about_description_2') }}
                </p>
            </div>

            {{-- قسم رسالتنا ورؤيتنا --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- رسالتنا --}}
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-start text-right">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xl font-bold text-slate-800">
                            {{ __('site.our_mission') }}
                        </h3>
                    </div>
                    <p class="text-slate-600 leading-loose text-sm md:text-base font-medium">
                        {{ __('site.our_mission_desc') }}
                    </p>
                </div>

                {{-- رؤيتنا --}}
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col items-start text-right">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                        <h3 class="text-xl font-bold text-slate-800">
                            {{ __('site.our_vision') }}
                        </h3>
                    </div>
                    <p class="text-slate-600 leading-loose text-sm md:text-base font-medium">
                        {{ __('site.our_vision_desc') }}
                    </p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>