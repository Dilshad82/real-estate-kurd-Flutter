<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('site.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- بطاقة الترحيب --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="text-slate-800 font-bold text-base">
                    {{ __('site.welcome_logged_in') }}
                </div>
            </div>

            {{-- قسم عرض العقارات المتجاوب --}}
            <div class="pt-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">
                            {{ app()->getLocale() == 'ku' ? 'تەزا ترین عەقارێن هاتینە زێدەکرن' : 'أحدث العقارات المضافة' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ app()->getLocale() == 'ku' ? 'عەقارێن بەردهەست بۆ فرۆتنێ و كرێکرنێ ببينا' : 'استعرض أحدث العقارات المتاحة للبيع وللإيجار' }}
                        </p>
                    </div>
                </div>

                {{-- استدعاء مكون شبكة العقارات التفاعلية --}}
                <x-property-grid :properties="$properties ?? []" />
            </div>

        </div>
    </div>
</x-app-layout>