<?php

namespace App\Repositories\TourRequire;

use App\Core\AbstractBaseRepository;
use App\Models\TourRequire;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class TourRequireRepository extends AbstractBaseRepository implements TourRequireInterface
{

    public function __construct(TourRequire $model)
    {
        parent::__construct($model);
    }

    public function fetchAllTourRequire()
    {
        $keyCache = ALL_TOUR_REQUIRE;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->model->all();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTourRequireById(int $id)
    {
        $keyCache = TOUR_REQUIRE_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->model->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function createTourRequire(array $data)
    {
        $tour = $this->model->create($data);

        $this->clearCache();

        return $tour;
    }

    public function updateTourRequire(int $id, array $data)
    {
        $tour = $this->find($id);
        if (!$tour) return null;

        $tour->update($data);

        $this->clearCache();
        return $tour;
    }

    public function deleteTourRequire(int $id)
    {
       $result =  $this->delete($id);
       if ($result) {
           $this->clearCache();
           return true;
       }
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
            ALL_TOUR_REQUIRE,
        ]);

        $patterns = [
            TOUR_REQUIRE_ID . '*',
        ];

        foreach ($patterns as $pattern) {
            $keys = $store->keys($pattern);
            if (!empty($keys)) {
                $store->del($keys);
            }
        }
    }
}
