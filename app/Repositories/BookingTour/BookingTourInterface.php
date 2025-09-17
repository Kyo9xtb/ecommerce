<?php

namespace App\Repositories\BookingTour;


interface BookingTourInterface
{
    public function fetchAll();
    public function findBookingTourById(int $id);
    public function createBookingTour(array $data);
    public function updateBookingTour(int $id, array $data);
    public function deleteBookingTour(int $id);
    public function filterBookingByDate(string $date);
    public function filterBookingByMonth(string $month);
}
