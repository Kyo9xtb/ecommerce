<?php

namespace App\Repositories\TourRequire;

use App\Core\AbstractBaseRepository;
use App\Models\TourRequire;
use App\Trait\HasModelCache;

class TourRequireRepository extends AbstractBaseRepository implements TourRequireInterface
{
    use HasModelCache;

    public function __construct(TourRequire $model)
    {
        parent::__construct($model);
    }

    public function fetchAllTourRequire()
    {
        $keyCache = ALL_TOUR_REQUIRE;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTourRequireById(int $id)
    {
        $keyCache = TOUR_REQUIRE_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTourRequireTourCode($code)
    {
        return $this->queryWithRelations()->where('tour_code', $code)->first();
    }

    public function createTourRequire(array $data)
    {
        $tour =  $this->model->create(
            array_intersect_key($data, array_flip([
                'tour_code',
                'full_name',
                'nationality',
                'email',
                'phone',
                'expected_destination',
                'tour_dates',
                'departure_date',
                'end_date',
                'expected_month',
                'expected_year',
                'number_days',
                'vehicle',
                'adult',
                'children',
                'baby',
                'number_rooms',
                'hotel_standards',
                'feedback_method',
                'status',
            ]))
        );

        $tour->detail()->create(
            array_intersect_key($data, array_flip([
                'price',
                'tour_program',
                'tour_policy',
                'terms_conditions',
            ]))
        );

        $this->clearCacheModel();

        return $tour;
    }

    public function updateTourRequire(int $id, array $data)
    {
        $tour = $this->find($id);
        if (!$tour) return null;

        $tour->update(
            array_intersect_key($data, array_flip([
                'full_name',
                'nationality',
                'email',
                'phone',
                'expected_destination',
                'tour_dates',
                'departure_date',
                'end_date',
                'expected_month',
                'expected_year',
                'number_days',
                'vehicle',
                'adult',
                'children',
                'baby',
                'number_rooms',
                'hotel_standards',
                'feedback_method',
                'status',
            ]))
        );

        $tour->detail()->updateOrCreate([], array_intersect_key($data, array_flip([
            'price',
            'tour_program',
            'tour_policy',
            'terms_conditions',
        ])));

        $this->clearCacheModel();
        return $tour;
    }

    public function deleteTourRequire(int $id)
    {
        $tour = $this->find($id);
        if (!$tour) return false;

        $tour->detail()?->delete();
        $tour->delete();
        $this->clearCacheModel();

        return true;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_TOUR_REQUIRE],
            'patterns' => [TOUR_REQUIRE_ID . '*'],
        ]);
    }

    private function queryWithRelations()
    {
        return $this->model->with(['detail']);
    }
}
