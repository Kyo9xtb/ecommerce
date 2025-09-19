<?php

namespace App\Repositories\Tour;

use App\Core\AbstractBaseRepository;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class TourRepository extends AbstractBaseRepository implements TourInterface
{

    public function __construct(Tour $model)
    {
        parent::__construct($model);
    }

    // Get all tours with details and images
    public function fetchAll()
    {
        $keyCache = ALL_TOUR;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
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

        if ($cachedData instanceof Collection) {
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

        if ($cachedData instanceof Collection) {
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

        if ($cachedData instanceof Collection) {
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

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->where('slug', $slug)->first();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTourName(string $name)
    {
        return Tour::query()->where('tour_name', $name)->first();
    }

    public function createTour(array $data): Tour
    {
        // $tour = $this->model->create([
        //     'tour_name' => $data['tour_name'] ?? null,
        //     'slug' => $data['slug'] ?? null,
        //     'price' => $data['price'] ?? 0,
        //     'sale' => $data['sale'] ?? 0,
        //     'trip' => $data['trip'] ?? null,
        //     'time' => $data['time'] ?? null,
        //     'status' => $data['status'] ?? 1,
        //     'area' => $data['area'] ?? null,
        //     'tour_group' => $data['tour_group'] ?? 1,
        // ]);
        // dd($data);

        $tour =  $this->model->create(
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
                'thumbnail'
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
            foreach ($data['vehicles'] as $vehicle) {
                $tour->vehicles()->create([
                    'code_vehicle' => $vehicle ?? 1,
                ]);
            }
        }

        $this->clearCache();

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
                'thumbnail'
            ]))
        );

        $tour->detail()->updateOrCreate([], array_intersect_key($data, array_flip([
            'tour_summary',
            'tour_program',
            'tour_policy',
            'terms_conditions',
        ])));

        $tour->images()?->delete();
        $tour->vehicles()?->delete();

        if (!empty($data['images']) && is_array($data['images'])) {
            $validImages = array_map(function ($image) {
                return ['image' => $image];
            }, array_filter($data['images']));

            if (!empty($validImages)) {
                $tour->images()->createMany($validImages);
            }
        }

        // if (!empty($data['vehicles']) && is_array($data['vehicles'])) {
        //     foreach ($data['vehicles'] as $vehicle) {
        //         $tour->vehicles()->create([
        //             'code_vehicle' => $vehicle ?? 1,
        //         ]);
        //     }
        // }
        $this->clearCache();
        return $tour;
    }

    public function deleteTour(int $id): bool
    {
        $tour = $this->find($id);
        if (!$tour) return false;

        $tour->images()->delete();
        $tour->detail()->delete();
        $tour->delete();

        $this->clearCache();
        return true;
    }



    private function getCacheKey(string $key): mixed
    {
        $store = Redis::connection();
        $result = $store->get($key);

        if ($result === '@null') {
            return null;
        }

        if (!empty($result)) {
            try {
                $data = unserialize($result);
                return is_array($data) ? collect($data) : $data;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    private function setCacheKey(string $key, mixed $data, int $ttl = 3600)
    {
        $store = Redis::connection();

        if (empty($data)) {
            $store->setex($key, $ttl, '@null');
        } else {
            $store->setex($key, $ttl, serialize($data->toArray()));
        }
    }


    private function clearCache(): void
    {
        $store = Redis::connection();

        $store->del([
            ALL_TOUR,
            TOUR_ACTIVE,
            TOUR_INACTIVE,
        ]);

        $patterns = [
            TOUR_ID . '*',
            TOUR_SLUG . '*',
        ];

        foreach ($patterns as $pattern) {
            $keys = $store->keys($pattern);
            if (!empty($keys)) {
                $store->del($keys);
            }
        }
    }

    private function queryWithRelations()
    {
        return $this->model->with(['detail', 'images', 'vehicles']);
    }
}
