<?php

namespace App\Repositories\TouristDestination;


interface TouristDestinationInterface
{
    public function fetchAll();
    public function findTouristDestinationById(int $id);
    public function findTouristDestinationBySlug(string $slug);
    public function findTouristDestinationByName(string $name);
    public function createTouristDestination(array $data);
    public function updateTouristDestination(int $id, array $data);
    public function deleteTouristDestination(int $id);
}
