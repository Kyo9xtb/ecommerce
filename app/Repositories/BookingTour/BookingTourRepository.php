<?php

namespace App\Repositories\BookingTour;

use App\Core\AbstractBaseRepository;
use App\Models\BookingTour;

class BookingTourRepository extends AbstractBaseRepository implements BookingTourInterface
{

    public function __construct(BookingTour $model)
    {
        parent::__construct($model);
    }

    public function fetchAll()
    {
        return $this->queryWithRelations()->get();
    }

    public function findBookingTourById(int $id)
    {
        return $this->queryWithRelations()->find($id);
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

    public function findBookingTourByCode(string $code)
    {
        return $this->model->firstWhere('booking_code', $code);
    }

    public function createBookingTour(array $data)
    {
        $tour = $this->model->create(array_intersect_key($data, array_flip([
            'user_id',
            'booking_code',
            'full_name',
            'email',
            'phone',
            'currency',
            'address',
            'note',
            'total_price',
            'deposit',
            'payment_method',
            'status',
        ])));


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

        return $tour->fresh();
    }

    public function updateBookingTour(int $id, array $data)
    {

        $tour = $this->find($id);
        if (!$tour) return null;

        $tour->update(array_intersect_key($data, array_flip([
            'user_id',
            'full_name',
            'email',
            'phone',
            'currency',
            'address',
            'note',
            'total_price',
            'deposit',
            'payment_method',
            'status',
        ])));

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

        return $tour->fresh();
    }

    public function deleteBookingTour(int $id): bool
    {
        $bookingTour = $this->find($id);
        if (!$bookingTour) return false;

        $bookingTour->details()->delete();
        $bookingTour->delete();

        return true;
    }

    private function queryWithRelations()
    {
        return $this->model->with(['details']);
    }
}
