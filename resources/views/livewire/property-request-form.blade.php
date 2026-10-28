<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-lg dir-rtl">
    @if(!$isSubmitted)
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            طلب عقار حسب المواصفات
        </h2>

        <form wire:submit.prevent="submitRequest" class="space-y-6">
            {{-- بيانات الزبون --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">الاسم الكامل</label>
                    <input type="text" wire:model="client_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border text-gray-900 bg-white" placeholder="أدخل اسمك">
                    @error('client_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">رقم الهاتف (الواتساب)</label>
                    <input type="text" wire:model="client_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border text-gray-900 bg-white" placeholder="07xxxxxxxxx">
                    @error('client_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- المحافظة، المنطقة، ونوع العقار --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- المحافظة --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">المحافظة</label>
                    <select wire:model.live="city_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-white text-gray-900 font-medium">
                        <option value="" class="bg-white text-gray-900">اختر المحافظة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" class="bg-white text-gray-900">{{ $city->display_name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- المنطقة --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">المنطقة</label>
                    <select wire:model="district_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-white text-gray-900 font-medium" @if(empty($districts)) disabled @endif>
                        <option value="" class="bg-white text-gray-900">{{ empty($districts) ? 'اختر المحافظة أولاً' : 'كل المناطق' }}</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" class="bg-white text-gray-900">{{ $district->display_name }}</option>
                        @endforeach
                    </select>
                    @error('district_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- نوع العقار --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">نوع العقار</label>
                    <select wire:model="property_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-white text-gray-900 font-medium">
                        <option value="" class="bg-white text-gray-900">اختر النوع</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}" class="bg-white text-gray-900">{{ $type->display_name }}</option>
                        @endforeach
                    </select>
                    @error('property_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- نطاق السعر والمساحة --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر ($)</label>
                    <div class="flex gap-2">
                        <input type="number" wire:model="min_price" placeholder="من" class="w-1/2 p-2 border rounded bg-white text-gray-900">
                        <input type="number" wire:model="max_price" placeholder="إلى" class="w-1/2 p-2 border rounded bg-white text-gray-900">
                    </div>
                    @error('min_price') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    @error('max_price') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المساحة (م²)</label>
                    <div class="flex gap-2">
                        <input type="number" wire:model="min_area" placeholder="من" class="w-1/2 p-2 border rounded bg-white text-gray-900">
                        <input type="number" wire:model="max_area" placeholder="إلى" class="w-1/2 p-2 border rounded bg-white text-gray-900">
                    </div>
                    @error('min_area') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                    @error('max_area') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition duration-200">
                إرسال الطلب والبحث عن مطابقة
            </button>
        </form>
    @else
        {{-- عرض نتائج المطابقة --}}
        <div class="space-y-4">
            <h3 class="text-xl font-bold">العقارات المطابقة ({{ count($matchedProperties) }})</h3>
            
            @if(count($matchedProperties) > 0)
                @foreach($matchedProperties as $property)
                    <div class="border p-4 rounded shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h4 class="font-bold text-lg">${{ number_format($property->price) }}</h4>
                            <p class="text-sm text-gray-600">المساحة: {{ $property->area }}م² | غرف: {{ $property->bedrooms }}</p>
                            <p class="text-xs text-gray-500 mt-1">المكتب: {{ $property->office->name ?? 'غير محدد' }}</p>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $property->office->phone_whatsapp ?? '') }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                            تواصل واتساب 💬
                        </a>
                    </div>
                @endforeach
            @else
                <div class="text-center py-6 bg-gray-50 rounded-lg">
                    <p class="text-gray-600">تم تسجيل طلبك بنجاح! لم نجد عقارات مطابقة حالياً، ولكن تم تحويل طلبك لجميع المكاتب العقارية وسيتم التواصل معك عند التوفر.</p>
                </div>
            @endif

            <button wire:click="$set('isSubmitted', false)" class="text-blue-600 underline text-sm mt-4 block">
                تقديم طلب جديد
            </button>
        </div>
    @endif
</div>