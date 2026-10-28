<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- الهيدر الرئيسي للصفحة --}}
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ __('site.contact_title') }}
                </h1>
                <p class="text-base text-slate-600">
                    {{ __('site.contact_subtitle') }}
                </p>
            </div>

            {{-- المحتوى الرئيسي: شبكة من عمودين --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                {{-- القسم الأيسر: معلومات التواصل --}}
                <div class="lg:col-span-1 space-y-4">
                    
                    {{-- بطاقة البريد الإلكتروني --}}
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 font-bold text-xl">
                            ✉
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('site.email') }}</h3>
                            <p class="text-xs text-slate-600 dir-ltr text-right">support@example.com</p>
                        </div>
                    </div>

                    {{-- بطاقة الهاتف --}}
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 font-bold text-xl">
                            📞
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('site.phone') }}</h3>
                            <p class="text-xs text-slate-600 dir-ltr text-right">+964 750 000 0000</p>
                        </div>
                    </div>

                    {{-- بطاقة العنوان --}}
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 font-bold text-xl">
                            📍
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">{{ __('site.address') }}</h3>
                            <p class="text-xs text-slate-600">{{ __('site.location_address') }}</p>
                        </div>
                    </div>

                </div>

                {{-- القسم الأيمن: نموذج إرسال الرسالة --}}
                <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200/80 shadow-sm">
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    {{ __('site.name') }}
                                </label>
                                <input type="text" name="name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:outline-none transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    {{ __('site.email') }}
                                </label>
                                <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                {{ __('site.message') }}
                            </label>
                            <textarea name="message" rows="5" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white focus:outline-none transition"></textarea>
                        </div>

                        <div>
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                                {{ __('site.send_message') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>