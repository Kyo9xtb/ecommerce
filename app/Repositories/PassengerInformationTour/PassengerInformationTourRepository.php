<?php

namespace App\Repositories\PassengerInformationTour;

use App\Core\AbstractBaseRepository;
use App\Models\PassengerInformationTour;

class PassengerInformationTourRepository extends AbstractBaseRepository implements PassengerInformationTourInterface
{
    public function __construct(PassengerInformationTour $model)
    {
        parent::__construct($model);
    }

    public function filterPassengersBookingTour($bookingId, $tourId)
    {
        return $this->model->where('booking_id', $bookingId)->where('tour_id', $tourId)->get();
    }

    public function findPassengerById($id)
    {
        return $this->find($id);
    }

    public function createPassenger(array $data)
    {
        return $this->create($data);
    }

    public function updatePassenger(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    public function deletePassenger(int $id)
    {
        return $this->delete($id);
    }

    public function getPassengersByTour($tourId)
    {
        return $this->model->where('tour_id', $tourId)->get();
    }

    public function countPassengersByTour($tourId)
    {
        return $this->model->where('tour_id', $tourId)->count();
    }
}
