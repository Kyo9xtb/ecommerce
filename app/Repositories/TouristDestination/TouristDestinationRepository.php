<?php

namespace App\Repositories\TouristDestination;

use App\Core\AbstractBaseRepository;
use App\Models\TouristDestination;
use App\Trait\HasModelCache;

class TouristDestinationRepository extends AbstractBaseRepository implements TouristDestinationInterface
{
    use HasModelCache;

    public function __construct(TouristDestination $model)
    {
        parent::__construct($model);
    }

    // Get all tours with details and images
    public function fetchAll()
    {
        $keyCache = ALL_TOURIST_DESTINATIONS;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->get();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get tour by id with details and images
    public function findTouristDestinationById(int $id)
    {
        $keyCache = TOURIST_DESTINATION_ID . $id;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->find($id);

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    // Get tour by slug with details and images
    public function findTouristDestinationBySlug(string $slug)
    {
        $keyCache = TOURIST_DESTINATION_SLUG . $slug;
        $cachedData = $this->getCacheKey($keyCache);

        if ($cachedData) {
            return $cachedData;
        }

        $result = $this->queryWithRelations()->where('slug', $slug)->first();

        $this->setCacheKey($keyCache, $result);

        return $result;
    }

    public function findTouristDestinationByName(string $name)
    {
        return $this->queryWithRelations()->where('place_name', $name)->first();
    }

    public function createTouristDestination(array $data)
    {
        $tour =  $this->model->create(
            array_intersect_key($data, array_flip([
                'place_name',
                'meta_title',
                'slug',
                'description',
                'details',
                'tour_group',
                'area',
                'thumbnail',
                'status',
            ]))
        );

        if (!empty($data['images']) && is_array($data['images'])) {
            $validImages = array_map(function ($image) {
                return ['image' => $image];
            }, array_filter($data['images']));

            if (!empty($validImages)) {
                $tour->images()->createMany($validImages);
            }
        }

        $this->clearCacheModel();

        return $tour;
    }

    public function updateTouristDestination(int $id, array $data)
    {
        $tour = $this->find($id);
        $tour->update(
            array_intersect_key($data, array_flip([
                'place_name',
                'meta_title',
                'slug',
                'description',
                'details',
                'tour_group',
                'area',
                'thumbnail',
                'status',
            ]))
        );

        if (!empty($data['images']) && is_array($data['images'])) {

            $validImages = array_map(function ($image) {
                return ['image' => $image];
            }, array_filter($data['images']));

            $oldImages  = $tour->images()->pluck('image')->toArray();

            $newImages = array_column($validImages, 'image');

            $toDelete = array_diff($oldImages, $newImages);
            $toAdd = array_diff($newImages, $oldImages);

            if (!empty($toDelete)) {
                $tour->images()->whereIn('image', $toDelete)->delete();
            }

            if (!empty($toAdd)) {
                $tour->images()->createMany(
                    array_map(fn($img) => ['image' => $img], $toAdd)
                );
            }
         
        }

        $this->clearCacheModel();

        return $tour;
    }

    public function deleteTouristDestination(int $id): bool
    {
        $tour = $this->find($id);
        if (!$tour) return false;

        $tour->images()?->delete();
        $tour->delete();

        $this->clearCacheModel();
        return true;
    }

    private function clearCacheModel()
    {
        $this->clearCache([
            'direct' => [ALL_TOURIST_DESTINATIONS],
            'patterns' => [TOURIST_DESTINATION_ID . '*', TOURIST_DESTINATION_SLUG . '*'],
        ]);
    }

    private function queryWithRelations()
    {
        return $this->model->with(['images']);
    }
}
