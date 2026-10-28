{{-- 
  ==========================================================================
  مكون بطاقة العقار (Property Card Component)
  --------------------------------------------------------------------------
  الوظيفة:
  - عرض تفاصيل العقار بتصميم أنيق ومتجاوب مع جميع أحجام الشاشات.
  - يتضمن صورة العقار، شارة العرض (للبيع/للإيجار)، السعر، والمواصفات الأساسية.
  ==========================================================================
--}}

@props(['property'])

<div class="group bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col justify-between h-full">
    
    {{-- قسم الصورة وشارات العرض --}}
    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
        {{-- صورة العقار --}}
        <img 
            src="{{ $property->main_image ?? asset('images/property-placeholder.jpg') }}" 
            alt="{{ $property->title ?? 'عقار' }}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
            loading="lazy"
        />

        {{-- شارة نوع العرض --}}
        <div class="absolute top-3 right-3 rtl:right-3 rtl:left-auto ltr:left-3 ltr:right-auto">
            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg text-white shadow-sm backdrop-blur-md {{ isset($property->type) && $property->type === 'sale' ? 'bg-emerald-600/90' : 'bg-indigo-600/90' }}">
                {{ isset($property->type) && $property->type === 'sale' ? 'للبيع' : 'للإيجار' }}
            </span>
        </div>

        {{-- شارة السعر --}}
        <div class="absolute bottom-3 right-3 rtl:right-3 rtl:left-auto ltr:left-3 ltr:right-auto bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-lg text-white">
            <span class="text-xs sm:text-sm font-black">${{ number_format($property->price ?? 0) }}</span>
        </div>
    </div>

    {{-- محتوى تفاصيل العقار --}}
    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
        
        <div>
            {{-- الموقع الجغرافي --}}
            <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="truncate">{{ $property->city ?? 'دهوك' }}، {{ $property->district ?? 'المركز' }}</span>
            </div>

            {{-- عنوان العقار --}}
            <h3 class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-indigo-600 transition-colors line-clamp-1">
                {{ $property->title ?? 'عنوان العقار غير محدد' }}
            </h3>
        </div>

        {{-- المواصفات (الغرف، الحمامات، المساحة) --}}
        <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs text-slate-500">
            <div class="flex flex-col items-center justify-center p-1.5 bg-slate-50 rounded-lg">
                <span class="font-bold text-slate-700">{{ $property->bedrooms ?? 0 }}</span>
                <span class="text-[10px] text-slate-400">غرف</span>
            </div>
            <div class="flex flex-col items-center justify-center p-1.5 bg-slate-50 rounded-lg">
                <span class="font-bold text-slate-700">{{ $property->bathrooms ?? 0 }}</span>
                <span class="text-[10px] text-slate-400">حمامات</span>
            </div>
            <div class="flex flex-col items-center justify-center p-1.5 bg-slate-50 rounded-lg">
                <span class="font-bold text-slate-700">{{ $property->area ?? 0 }}m²</span>
                <span class="text-[10px] text-slate-400">المساحة</span>
            </div>
        </div>

        {{-- زر التفاصيل --}}
        <div class="pt-2">
            <a href="{{ route('properties.show', $property->id ?? 1) }}" class="w-full inline-flex items-center justify-center py-2 px-4 bg-slate-100 hover:bg-slate-900 text-slate-700 hover:text-white text-xs font-bold rounded-xl transition-all duration-200">
                عرض التفاصيل
            </a>
        </div>

    </div>
</div>