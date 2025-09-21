<?php

namespace App\Repositories\PassengerInformationTour;


interface PassengerInformationTourInterface
{
    public function filterPassengersBookingTour($bookingId, $tourId);
    public function findPassengerById($id);
    public function createPassenger(array $data);
    public function updatePassenger(int $id, array $data);
    public function deletePassenger(int $id);
    public function getPassengersByTour($tourId);
    public function countPassengersByTour($tourId);
}
