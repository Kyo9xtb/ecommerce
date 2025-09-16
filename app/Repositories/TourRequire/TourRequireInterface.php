<?php

namespace App\Repositories\TourRequire;


interface TourRequireInterface
{
    public function fetchAllTourRequire();
    public function findTourRequireById(int $id);
    public function createTourRequire(array $data);
    public function updateTourRequire(int $id, array $data);
    public function deleteTourRequire(int $id);
}
