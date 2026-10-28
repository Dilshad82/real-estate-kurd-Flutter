<x-app-layout>
    {{-- 
        ====================================================================================
        واجهة إدارة المكاتب العقارية المحدثة بتصميم عصري واحترافي
        - القفل التفاعلي بدون مكتبات خارجية (Pure JS Protection)
        ====================================================================================
    --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600/10 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-xs">
                    🏢
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-base leading-snug">
                        {{ app()->getLocale() == 'ku' ? 'بەڕێوەبردنی نوسینگەکانی خانوبەرە و داواکارییەکان' : 'إدارة المكاتب العقارية وطلبات التسجيل' }}
                    </h2>
                    <p class="text-xs text-slate-400 font-medium">
                        {{ app()->getLocale() == 'ku' ? 'کۆنتڕۆڵکرنا مۆڵەتان و ڕێپێدانا پارێزگەهان' : 'التحكم بتراخيص المكاتب وصلاحيات النشر في المحافظات' }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. رسائل التنبيه والنجاح --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200/80 text-emerald-800 rounded-2xl shadow-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                            ✓
                        </div>
                        <span class="font-bold text-xs">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-base px-2 rounded-lg hover:bg-emerald-100/50 transition cursor-pointer">&times;</button>
                </div>
            @endif

            {{-- 2. البطاقة الرئيسية وجدول المكاتب --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200/80">
                
                {{-- هيدر البطاقة --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
                        <h3 class="font-bold text-slate-900 text-sm">
                            {{ app()->getLocale() == 'ku' ? 'لیستی داواکارییەکان و نوسینگەکان' : 'قائمة طلبات التسجيل والمكاتب العقارية' }}
                        </h3>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-full text-xs font-black shadow-2xs">
                        {{ app()->getLocale() == 'ku' ? 'ژمارە:' : 'العدد:' }} {{ $offices->total() }}
                    </span>
                </div>

                {{-- جدول عرض المكاتب التفاعلي --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs text-slate-600">
                        <thead class="bg-slate-50/90 text-slate-500 font-bold border-b border-slate-100 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'ناوی نوسینگە' : 'اسم المكتب' }}</th>
                                <th class="px-6 py-4 whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'ئيمێڵ' : 'البريد الإلكتروني' }}</th>
                                <th class="px-6 py-4 whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'پارێزگا' : 'المحافظة' }}</th>
                                <th class="px-6 py-4 whitespace-nowrap min-w-[300px]">{{ app()->getLocale() == 'ku' ? 'پارێزگەهێن ڕێپێدايی بۆ بەلاڤکرنێ' : 'المحافظات المسموح بالنشر فيها' }}</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'مۆڵەتی نوسینگە' : 'إجازة المكتب' }}</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'دۆخی هەژمار' : 'حالة الحساب' }}</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">{{ app()->getLocale() == 'ku' ? 'کردارەکان' : 'الإجراءات' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($offices as $office)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    
                                    {{-- 1. اسم المكتب --}}
                                    <td class="px-6 py-5 font-bold text-slate-900 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-extrabold text-slate-900">{{ $office->name }}</span>
                                            @if(!empty($office->is_admin) || in_array(mb_strtolower(trim($office->role ?? '')), ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']) || str_starts_with($office->role ?? '', 'admin_'))
                                                <span class="inline-flex items-center gap-1 w-fit px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-[10px] font-bold rounded-md">
                                                    👑 {{ app()->getLocale() == 'ku' ? 'ئەدمین' : 'أدمن' }} 
                                                    ({{ str_replace('admin_', '', $office->role) }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- 2. البريد --}}
                                    <td class="px-6 py-5 font-medium text-slate-600 whitespace-nowrap dir-ltr text-right">{{ $office->email }}</td>
                                    
                                    {{-- 3. المحافظة الرئيسية --}}
                                    <td class="px-6 py-5 font-bold text-slate-700 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 px-2.5 py-1 rounded-lg border border-slate-200/60">
                                            📍 {{ $office->city ? (is_array($office->city->name) ? ($office->city->name[app()->getLocale()] ?? reset($office->city->name)) : $office->city->name) : 'غير محدد' }}
                                        </span>
                                    </td>
                                    
                                    {{-- 4. المحافظات المسموح بها --}}
                                    <td class="px-6 py-5">
                                        <form action="{{ route('admin.offices.update', $office->id) }}" method="POST" class="space-y-2">
                                            @csrf
                                            @method('PUT')

                                            @php
                                                $allCities = \App\Models\City::all();
                                                $allowedIds = $office->allowedCities->pluck('id')->toArray();
                                                if (empty($allowedIds) && $office->city_id) {
                                                    $allowedIds = [$office->city_id];
                                                }
                                            @endphp

                                            <div class="flex flex-wrap gap-1.5 items-center">
                                                @foreach($allCities as $c)
                                                    @php
                                                        $cName = is_array($c->name) 
                                                            ? ($c->name[app()->getLocale()] ?? reset($c->name)) 
                                                            : $c->name;
                                                        $isChecked = in_array($c->id, $allowedIds);
                                                    @endphp
                                                    <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold cursor-pointer transition-all border {{ $isChecked ? 'bg-indigo-50/80 border-indigo-300 text-indigo-700 shadow-2xs' : 'bg-slate-50 border-slate-200 text-slate-500 hover:bg-slate-100' }}">
                                                        <input type="checkbox" 
                                                               name="allowed_cities[]" 
                                                               value="{{ $c->id }}" 
                                                               {{ $isChecked ? 'checked' : '' }}
                                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/30 w-3.5 h-3.5 cursor-pointer">
                                                        <span>{{ $cName }}</span>
                                                    </label>
                                                @endforeach
                                            </div>

                                            <div class="pt-0.5">
                                                <button type="submit" class="inline-flex items-center gap-1 text-[10px] bg-slate-900 hover:bg-indigo-600 text-white font-bold px-3 py-1 rounded-lg transition-all shadow-xs cursor-pointer">
                                                    <span>💾</span>
                                                    <span>{{ app()->getLocale() == 'ku' ? 'تۆمارکرن' : 'حفظ الصلاحيات' }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    {{-- 5. إجازة المكتب مع دالة فتح القفل --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @if($office->office_license)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::url($office->office_license) }}" 
                                               target="_blank" 
                                               onclick="enableApproveButton('{{ $office->id }}')" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200/80 transition shadow-2xs cursor-pointer">
                                                <span>🔎</span>
                                                <span>{{ app()->getLocale() == 'ku' ? 'بینینی بڕوانامە' : 'معاينة الملف' }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-[11px] font-medium bg-slate-100 px-2.5 py-1 rounded-lg">{{ app()->getLocale() == 'ku' ? 'بڕوانامە نییە' : 'لا يوجد ملف' }}</span>
                                        @endif
                                    </td>
                                    
                                    {{-- 6. حالة الحساب --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @if($office->is_approved)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>{{ app()->getLocale() == 'ku' ? 'چالاککری' : 'مُفعل' }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                                                <span>⏳</span>
                                                <span>{{ app()->getLocale() == 'ku' ? 'ل تەنگاڤیا پەسەندکرنێ' : 'بانتظار الموافقة' }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- 7. الإجراءات مع القفل والموافقة المشروطة بـ JS --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            @if(!$office->is_approved)
                                                <form action="{{ route('admin.offices.approve', $office->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')

                                                    {{-- زر التفعيل المعطل تلقائياً بشرط المعاينة --}}
                                                    <button type="submit" 
                                                            id="approve-btn-{{ $office->id }}" 
                                                            disabled 
                                                            onclick="return confirm('{{ app()->getLocale() == 'ku' ? 'دڵنیایت؟' : 'هل تأكدت من صحة إجازة المكتب بعد المعاينة وتريد تفعيله؟' }}')" 
                                                            class="px-3.5 py-1.5 bg-slate-200 text-slate-400 font-bold text-xs rounded-xl transition shadow-none cursor-not-allowed opacity-75 flex items-center gap-1">
                                                        <span id="approve-icon-{{ $office->id }}">🔒</span>
                                                        <span id="approve-text-{{ $office->id }}">{{ app()->getLocale() == 'ku' ? 'پێویستە بەڵگەنامە ببینی' : 'عاين الإجازة للتفعيل' }}</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.offices.reject', $office->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" onclick="return confirm('{{ app()->getLocale() == 'ku' ? 'دڵنیایت؟' : 'هل تريد إلغاء تفعيل هذا المكتب؟' }}')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl border border-rose-200/80 transition cursor-pointer flex items-center gap-1">
                                                        <span>✕</span>
                                                        <span>{{ app()->getLocale() == 'ku' ? 'هەڵوەشاندنەوە' : 'إلغاء التفعيل' }}</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @php
                                                $authUserRole = mb_strtolower(trim(auth()->user()->role ?? ''));
                                                $isAuthAdmin = !empty(auth()->user()->is_admin) || in_array($authUserRole, ['admin', 'ادمن', 'ادمان', 'أدمن', 'أدمان']);
                                            @endphp

                                            @if($isAuthAdmin)
                                                <form action="{{ route('admin.city_admins.make_office_admin') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="office_user_id" value="{{ $office->id }}">
                                                    
                                                    @php
                                                        $cityNameEn = strtolower(trim($office->city->name_en ?? $office->city->slug ?? ''));
                                                        if (empty($cityNameEn)) {
                                                            $cityNameEn = strtolower(trim($office->city->name ?? ''));
                                                        }
                                                        
                                                        if (str_contains($cityNameEn, 'duhok') || str_contains($cityNameEn, 'دهوك')) {
                                                            $targetRole = 'admin_duhok';
                                                        } elseif (str_contains($cityNameEn, 'erbil') || str_contains($cityNameEn, 'أربيل') || str_contains($cityNameEn, 'اربيل')) {
                                                            $targetRole = 'admin_erbil';
                                                        } elseif (str_contains($cityNameEn, 'sul') || str_contains($cityNameEn, 'سليمانية') || str_contains($cityNameEn, 'السليمانية')) {
                                                            $targetRole = 'admin_sulaymaniyah';
                                                        } else {
                                                            $targetRole = 'admin_' . ($office->city_id ?? 'city');
                                                        }

                                                        $cityNameDisplay = $office->city ? (is_array($office->city->name) ? ($office->city->name[app()->getLocale()] ?? reset($office->city->name)) : $office->city->name) : '';
                                                    @endphp

                                                    <input type="hidden" name="city_role" value="{{ $targetRole }}">

                                                    <button type="submit" onclick="return confirm('{{ app()->getLocale() == 'ku' ? 'دڵنیایت لە دەستنیشانکردنی وەک ئەدمینی پارێزگا؟' : 'هل أنت تأكد من تعيين هذا المكتب كـ أدمن للمحافظة التابع لها؟' }}')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs rounded-xl border border-amber-200/80 transition shadow-2xs whitespace-nowrap cursor-pointer flex items-center gap-1">
                                                        <span>👑</span>
                                                        <span>{{ app()->getLocale() == 'ku' ? 'تەعیین وەک ئەدمین' : 'تعيين كـ أدمن' }}</span>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs font-bold">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <span class="text-2xl">🏢</span>
                                            <span>{{ app()->getLocale() == 'ku' ? 'هیچ نوسینگەیەک تۆمار نەکراوە.' : 'لا يوجد مكاتب عقارية مسجلة حالياً.' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- روابط التصفح --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $offices->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- 
        ====================================================================================
        سكربت JavaScript بسيط للتحكم بالقفل وتفعيله فور الضغط على "معاينة الملف"
        ====================================================================================
    --}}
    <script>
        function enableApproveButton(officeId) {
            const btn = document.getElementById('approve-btn-' + officeId);
            const icon = document.getElementById('approve-icon-' + officeId);
            const text = document.getElementById('approve-text-' + officeId);
            
            if (btn) {
                // إلغاء الخاصية disabled
                btn.removeAttribute('disabled');
                
                // تغيير التصميم إلى اللون الأخضر القابل للنقر
                btn.className = "px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs cursor-pointer flex items-center gap-1";
                
                // تحديث الأيقونة والنص
                if (icon) icon.innerText = "✓";
                if (text) text.innerText = "{{ app()->getLocale() == 'ku' ? 'پەسەندکرن' : 'موافقة وتفعيل' }}";
            }
        }
    </script>
</x-app-layout>