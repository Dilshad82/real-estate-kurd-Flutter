{{-- 
  ==========================================================================
  مكون الشبكة التفاعلية (Property Grid Component)
  --------------------------------------------------------------------------
  الوظيفة:
  - توزيع بطاقات العقارات بشكل شبكي متجاوب (Responsive Grid):
    * الموبايل (grid-cols-1)
    * الشاشات المتوسطة (md:grid-cols-2)
    * الشاشات الكبيرة (lg:grid-cols-3)
    * الشاشات العريضة (xl:grid-cols-4)
  ==========================================================================
--}}

@props(['properties' => []])

@if(count($properties) > 0)
    {{-- الحاوية الشبكية المتجاوبة --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @foreach($properties as $property)
            <x-property-card :property="$property" />
        @endforeach
    </div>
@else
    {{-- حالة عدم وجود عقارات --}}
    <div class="flex flex-col items-center justify-center py-12 px-4 bg-white rounded-2xl border border-dashed border-slate-300 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 5h4m-4 0h4m-4-5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-700">لا توجد عقارات متاحة حالياً</h3>
        <p class="text-xs text-slate-500 mt-1">لم يتم العثور على أي نتائج تتطابق مع خيارات البحث الحالية.</p>
    </div>
@endif