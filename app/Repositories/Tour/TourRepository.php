<?php

namespace App\Repositories\Tour;

use App\Core\AbstractBaseRepository;
use App\Models\Tour;
use App\Trait\HasModelCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class TourRepository extends AbstractBaseRepository implements TourInterface
{
    use HasModelCache;

    public function __construct(Tour $model)
    {
        parent::__construct($model);
    }

    // Get all tours with details and images
    public function fetchAll()
    {
        $keyCache = ALL_TOUR;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get all active tours with details and images
    public function getAllTourActive()
    {
        $keyCache = TOUR_ACTIVE;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }
        $result = $this->queryWithRelations()->where('status', 1)->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get all Inactive tours with details and images

    public function getAllTourInactive()
    {
        $keyCache = TOUR_INACTIVE;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result =  $this->queryWithRelations()->where('status', 0)->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get tour by id with details and images
    public function findTourById(int $id)
    {
        $keyCache = TOUR_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get tour by slug with details and images
    public function findTourBySlug(string $slug)
    {
        $keyCache = TOUR_SLUG . $slug;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->where('slug', $slug)->first();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTourCode(string $code)
    {
        return $this->model->where('tour_code', $code)->first();
    }

    public function findTourName(string $name)
    {
        return Tour::query()->where('tour_name', $name)->first();
    }

    public function createTour(array $data): Tour
    {
        $tour =  $this->model->create(
            array_intersect_key($data, array_flip([
                'tour_code',
                'tour_name',
                'slug',
                'price',
                'sale',
                'trip',
                'time',
                'status',
                'area',
                'tour_group',
                'thumbnail',
                'departure_schedule'
            ]))
        );

        $tour->detail()->create(
            array_intersect_key($data, array_flip([
                'tour_summary',
                'tour_program',
                'tour_policy',
                'terms_conditions',
            ]))
        );

        if (!empty($data['images']) && is_array($data['images'])) {
            $validImages = array_map(function ($image) {
                return ['image' => $image];
            }, array_filter($data['images']));

            if (!empty($validImages)) {
                $tour->images()->createMany($validImages);
            }
        }

        if (!empty($data['vehicles']) && is_array($data['vehicles'])) {
            $validVehicles = array_map(function ($vehicle) {
                return ['code_vehicle' => (int)$vehicle['code_vehicle']];
            }, array_filter($data['vehicles']));

            if (!empty($validVehicles)) {
                $tour->vehicles()->createMany($validVehicles);
            }
        }

        if (!empty($data['guests']) && is_array($data['guests'])) {
            $validGuests = array_map(function ($guest) {
                return ['guest_code' => (int) $guest['guest_code']];
            }, array_filter($data['guests']));

            if (!empty($validGuests)) {
                $tour->guests()->createMany($validGuests);
            }
        }

        $this->clearCacheModel();

        return $tour;
    }

    public function updateTour(int $id, array $data)
    {
        $tour = $this->find($id);
        if (!$tour) return null;
        $tour->update(
            array_intersect_key($data, array_flip([
                'tour_name',
                'slug',
                'price',
                'sale',
                'trip',
                'time',
                'status',
                'area',
                'tour_group',
                'thumbnail',
                'departure_schedule',
            ]))
        );

        $tour->detail()->updateOrCreate([], array_intersect_key($data, array_flip([
            'tour_summary',
            'tour_program',
            'tour_policy',
            'terms_conditions',
        ])));

        if (!empty($data['images']) && is_array($data['images'])) {

            $validImages = array_map(function ($image) {
                return ['image' => $image];
            }, array_filter($data['images']));

            $oldImages  = $tour->images()->pluck('image')->toArray();

            $newImages = array_column($validImages, 'image');

            $toDelete = array_diff($oldImages, $newImages);
            $toAdd = array_diff($newImages, $oldImages);

            if (!empty($toDelete)) {
                $tour->images()->whereIn('image', $toDelete)->delete();
            }

            if (!empty($toAdd)) {
                $tour->images()->createMany(
                    array_map(fn($img) => ['image' => $img], $toAdd)
                );
            }
        }

        if (!empty($data['vehicles']) && is_array($data['vehicles'])) {
            $newVehicles = array_map(
                fn($vehicle) => (int) $vehicle['code_vehicle'],
                array_filter($data['vehicles'], fn($v) => !empty($v['code_vehicle']))
            );

            $oldVehicles = $tour->vehicles()->pluck('code_vehicle')->toArray();

            $toDelete = array_diff($oldVehicles, $newVehicles);
            $toAdd = array_diff($newVehicles, $oldVehicles);

            if ($toDelete) {
                $tour->vehicles()->whereIn('code_vehicle', $toDelete)->delete();
            }

            if ($toAdd) {
                $tour->vehicles()->createMany(
                    array_map(fn($v) => ['code_vehicle' => $v], $toAdd)
                );
            }
        }

        if (!empty($data['guests']) && is_array($data['guests'])) {
            $newGuests = array_map(
                fn($guest) => (int) $guest['guest_code'],
                array_filter($data['guests'], fn($g) => !empty($g['guest_code']))
            );

            $oldGuests = $tour->guests()->pluck('guest_code')->toArray();

            $toDelete = array_diff($oldGuests, $newGuests);
            $toAdd = array_diff($newGuests, $oldGuests);

            if ($toDelete) {
                $tour->guests()->whereIn('guest_code', $toDelete)->delete();
            }

            if ($toAdd) {
                $tour->guests()->createMany(
                    array_map(fn($g) => ['guest_code' => $g], $toAdd)
                );
            }
        }

        $this->clearCacheModel();
        return $tour;
    }

    public function deleteTour(int $id): bool
    {
        $tour = $this->find($id);
        if (!$tour) return false;

        $tour->images()?->delete();
        $tour->detail()?->delete();
        $tour->guests()?->delete();
        $tour->vehicles()?->delete();

        $tour->delete();

        $this->clearCacheModel();
        return true;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_TOUR],
            'patterns' => [TOUR_ID . '*', TOUR_SLUG . '*'],
        ]);
    }

    private function queryWithRelations()
    {
        return $this->model->with(['detail', 'images', 'vehicles', 'guests']);
    }
}
