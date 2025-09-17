<?php

namespace App\Repositories\BookingTour;

use App\Core\AbstractBaseRepository;
use App\Models\BookingTour;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class BookingTourRepository extends AbstractBaseRepository implements BookingTourInterface
{

    public function __construct(BookingTour $model)
    {
        parent::__construct($model);
    }

    public function fetchAll()
    {
        $keyCache = ALL_BOOKING_TOUR;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findBookingTourById(int $id)
    {
        $keyCache = BOOKING_TOUR_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData instanceof Collection) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function filterBookingByDate(string $date)
    {
        return $this->model->with(['details'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function filterBookingByMonth(string $month)
    {
        // $month dạng '2025-09'
        [$year, $monthNumber] = explode('-', $month);

        return $this->model
            ->with('details')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function createBookingTour(array $data)
    {
        $tour = $this->model->create([
            'user_id' => $data['user_id'] ?? null,
            'full_name' => $data['full_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'currency' => $data['currency'] ?? null,
            'address' => $data['address'] ?? null,
            'note' => $data['note'] ?? null,
            'total_price' => $data['total_price'] ?? 0,
            'deposit' => $data['deposit'] ?? 0,
            'payment_method' => $data['payment_method'] ?? 1,
            'status' => $data['status'] ?? 1,
        ]);


        if (!empty($data['details']) && is_array($data['details'])) {
            $details = array_map(function ($detail) {
                return [
                    'tour_id'        => $detail['tour_id'] ?? null,
                    'guest_id'       => $detail['guest_id'] ?? null,
                    'price'          => $detail['price'] ?? 0,
                    'quantity'       => $detail['quantity'] ?? 0,
                    'departure_date' => $detail['departure_date'] ?? null,
                ];
            }, $data['details']);

            $tour->details()->createMany($details);
        }

        $this->clearCache();

        return $tour->fresh();
    }

    public function updateBookingTour(int $id, array $data)
    {

        $tour = $this->find($id);
        if (!$tour) return null;

        $tour->update([
            'user_id' => $data['user_id'] ?? null,
            'full_name' => $data['full_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'currency' => $data['currency'] ?? null,
            'address' => $data['address'] ?? null,
            'note' => $data['note'] ?? null,
            'total_price' => $data['total_price'] ?? 0,
            'deposit' => $data['deposit'] ?? 0,
            'payment_method' => $data['payment_method'] ?? 1,
            'status' => $data['status'] ?? 1,
        ]);

        $tour->details()->delete();

        if (!empty($data['details']) && is_array($data['details'])) {
            $details = array_map(function ($detail) {
                return [
                    'tour_id'        => $detail['tour_id'] ?? null,
                    'guest_id'       => $detail['guest_id'] ?? null,
                    'price'          => $detail['price'] ?? 0,
                    'quantity'       => $detail['quantity'] ?? 0,
                    'departure_date' => $detail['departure_date'] ?? null,
                ];
            }, $data['details']);

            $tour->details()->createMany($details);
        }

        $this->clearCache();

        return $tour->fresh();
    }

    public function deleteBookingTour(int $id): bool
    {
        $bookingTour = $this->find($id);
        if (!$bookingTour) return false;

        $bookingTour->details()->delete();
        $bookingTour->delete();

        $this->clearCache();

        return true;
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
            ALL_BOOKING_TOUR
        ]);

        $patterns = [
            BOOKING_TOUR_ID . '*',
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
        return $this->model->with(['details']);
    }
}
