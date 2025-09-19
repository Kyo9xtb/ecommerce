<?php

namespace App\Repositories\News;

use App\Core\AbstractBaseRepository;
use App\Models\News;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class NewsRepository extends AbstractBaseRepository implements NewsInterface
{

    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    public function fetchAll()
    {
        $keyCache = ALL_NEWS;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->all();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findNewsById($id)
    {
        $keyCache = NEWS_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findNewsBySlug($slug)
    {
        $keyCache = NEWS_SLUG . $slug;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->model->where('slug', $slug)->first();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findNewsByTitle($title)
    {
        return $this->model->where('title', $title)->first();
    }

    public function createNews($data)
    {
        $result = $this->create($data);

        if (!$result) {
            return false;
        }

        $this->clearCache();

        return  $result;
    }

    public function updateNews($id, $data)
    {
        $result = $this->update($id, $data);

        if (!$result) {
            return false;
        }

        $this->clearCache();
        return  $result;
    }

    public function deleteNews($id)
    {
        $result = $this->delete($id);
        if (!$result) {
            return false;
        }

        $this->clearCache();
        return  $result;
    }

    // Cache
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

    private function setCacheKey(string $key, mixed $data, int $ttl = 3600 * 24 * 7)
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
            ALL_NEWS,
        ]);

        $patterns = [
            NEWS_ID,
            NEWS_SLUG,
        ];

        foreach ($patterns as $pattern) {
            $keys = $store->keys($pattern . '*');
            if (!empty($keys)) {
                $store->del($keys);
            }
        }
    }
}
