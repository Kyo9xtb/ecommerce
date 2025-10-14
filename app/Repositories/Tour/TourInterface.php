<?php

namespace App\Repositories\Tour;


interface TourInterface
{
    public function fetchAll();
    public function getAllTourActive();
    public function getAllTourInactive();
    public function findTourById(int $id);
    public function findTourBySlug(string $slug);
    public function findTourCode(string $code);
    public function findTourName(string $name);
    public function createTour(array $data);
    public function updateTour(int $id, array $data);
    public function deleteTour(int $id);
}
