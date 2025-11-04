<?php

namespace App\Repositories\BookingTour;

use App\Core\AbstractBaseInterface;

interface BookingTourInterface extends AbstractBaseInterface

{
    public function fetchAll();
    public function findBookingTourById(int $id);
    public function findBookingTourByCode(string $code);
    public function createBookingTour(array $data);
    public function updateBookingTour(int $id, array $data);
    public function deleteBookingTour(int $id);
    public function filterBookingByDate(string $date);
    public function filterBookingByMonth(string $month);
}
