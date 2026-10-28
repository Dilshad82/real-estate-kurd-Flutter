<?php
    namespace App\Services;

    use App\Models\Property;
    use App\Models\PropertyRequest;
    use Illuminate\Database\Eloquent\Collection;

    //ننشئ خدمة (Service Class) تقوم بمطابقة عروض المكاتب مع طلب الزبون أو العكس.
    class PropertyMatchingService
    {
        /**
         * البحث عن العقارات المعروضة التي تطابق طلب زبون معين
         */
        public function getMatchingPropertiesForRequest(PropertyRequest $request): Collection
        {
            return Property::query()
                ->where('city', $request->city)
                ->where('type', $request->type)
                ->whereBetween('price', [$request->min_price, $request->max_price])
                ->whereBetween('area', [$request->min_area, $request->max_area])
                ->where('bedrooms', '>=', $request->min_bedrooms)
                ->where('bathrooms', '>=', $request->min_bathrooms)
                ->when($request->max_age, function ($query, $maxAge) {
                    return $query->where('age', '<=', $maxAge);
                })
                ->where('status', 'available')
                ->with('office')
                ->get();
        }

        /**
         * تجلب للمكتب العقاري قائمة طلبات الزبائن التي تطابق عقارات هذا المكتب
         */
        public function getMatchingRequestsForOffice(int $officeId): Collection
        {
            $officeProperties = Property::where('office_id', $officeId)->get();

            return PropertyRequest::query()->get()->filter(function ($request) use ($officeProperties) {
                foreach ($officeProperties as $property) {
                    if (
                        $property->city === $request->city &&
                        $property->type === $request->type &&
                        $property->price >= $request->min_price && $property->price <= $request->max_price &&
                        $property->area >= $request->min_area && $property->area <= $request->max_area &&
                        $property->bedrooms >= $request->min_bedrooms &&
                        $property->bathrooms >= $request->min_bathrooms
                    ) {
                        return true;
                    }
                }
                return false;
            });
        }
    }