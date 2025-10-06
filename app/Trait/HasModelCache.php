<?php

namespace App\Trait;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

trait HasModelCache
{
    protected int $cacheTtl = 3600; // thời gian cache mặc định (giây)

    protected string $redisConnection = 'default';
    /**
     * Lấy dữ liệu từ cache Redis.
     */
    protected function getRedisConnect()
    {
        return Redis::connection($this->redisConnection);
    }

    protected function getCacheKey(string $key): mixed
    {
        try {
            $store = $this->getRedisConnect();
            $raw = $store->get($key);

            if (!$raw || $raw === '@null') {
                return null;
            }

            $data = unserialize($raw);

            // Nếu là danh sách (Collection)
            if (isset($data['__collection']) && $data['__collection'] === true) {
                return collect($data['items'])->map(fn($item) => (new $this->model)->forceFill($item));
            }

            // Nếu là model đơn
            if (is_array($data)) {
                return (new $this->model)->forceFill($data);
            }

            return $data;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Lưu dữ liệu vào cache Redis.
     */
    protected function setCacheKey(string $key, mixed $data, ?int $ttl = null): void
    {
        try {
            $store = $this->getRedisConnect();

            // Dùng TTL mặc định nếu không truyền vào
            $ttl ??= $this->cacheTtl;

            if (empty($data)) {
                $store->setex($key, $ttl, '@null');
                return;
            }

            if ($data instanceof Collection) {
                $payload = [
                    '__collection' => true,
                    'items' => $data->toArray(),
                ];
            } else {
                $payload = $data->toArray();
            }

            $store->setex($key, $ttl, serialize($payload));
        } catch (\Throwable $e) {
            // Ignore Redis errors
        }
    }

    /**
     * Xóa toàn bộ cache liên quan model hiện tại.
     */
    protected function clearCache(array $extraPatterns = []): void
    {
        try {
            $store = $this->getRedisConnect();

            // Danh sách key cần xóa trực tiếp
            $keys = $extraPatterns['direct'] ?? [];
            if (!empty($keys)) {
                $store->del($keys);
            }

            // Xóa theo pattern (dùng SCAN thay vì KEYS)
            $patterns = $extraPatterns['patterns'] ?? [];
            foreach ($patterns as $pattern) {
                $cursor = null;
                do {
                    [$cursor, $foundKeys] = $store->scan($cursor, ['MATCH' => $pattern, 'COUNT' => 100]);
                    if (!empty($foundKeys)) {
                        $store->del($foundKeys);
                    }
                } while ($cursor != 0);
            }
        } catch (\Throwable $e) {
            // Ignore Redis errors
        }
    }
}
