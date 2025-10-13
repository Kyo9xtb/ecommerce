<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\TouristDestinationResource;
use App\Http\Responses\Api\TouristDestinationResponse;
use App\Repositories\TouristDestination\TouristDestinationInterface;
use App\Trait\FileHandler;

class TouristDestinationHandler
{
    use FileHandler;

    public function __construct(
        public TouristDestinationInterface $touristDestinationInterface,
    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();

        return match ($method) {
            'GET' => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete((int) $command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    public function handleGet($command)
    {
        if ($slug = $command->request->route('slug')) {
            return $this->handleGetBySlug($slug);
        }
        if ($id = $command->id) {
            return $this->handleGetById($id);
        }

        return $this->handleFetchAll();
    }

    private function handleFetchAll(): TouristDestinationResponse
    {
        $result = $this->touristDestinationInterface->fetchAll();
        if (!$result) {
            throw new JsonApiException(
                'Failed to get tour destination information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TouristDestinationResponse(
            message: 'Get tour destination information successfully',
            data: TouristDestinationResource::collection($result)->toArray(request()),
        );
    }

    private function handleGetBySlug($slug): TouristDestinationResponse
    {
        $result = $this->touristDestinationInterface->findTouristDestinationBySlug($slug);

        if (!$result) {
            throw new JsonApiException(
                'Failed to get tour information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TouristDestinationResponse(
            message: 'Get tour information successfully',
            data: (new TouristDestinationResource($result))->resolve()
        );
    }

    private function handleGetById($id): TouristDestinationResponse
    {
        $result = $this->touristDestinationInterface->findTouristDestinationById($id);

        if (!$result) {
            throw new JsonApiException(
                'Failed to get tour destination information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TouristDestinationResponse(
            message: 'Get tour destination information successfully',
            data: (new TouristDestinationResource($result))->resolve()
        );
    }

    private function handleCreate($command)
    {
        $this->validateUnique($command);

        $fields = [
            'place_name',
            'meta_title',
            'slug',
            'description',
            'details',
            'tour_group',
            'area',
            'status',
        ];

        $inputData = array_filter(
            CommandDataHelper::extract($fields, $command),
            fn($value) => !is_null($value)
        );

        $thumbnailFiles = $command->request->file('thumbnail');
        $imageFiles = $command->request->file('images');

        $thumbnail = $this->processFiles($thumbnailFiles, 'tourist_destinations/temp');
        $images = $this->processFiles($imageFiles, 'tourist_destinations/temp');

        $inputData['thumbnail'] = $thumbnail ? $thumbnail[0] : null;
        $inputData['images'] = $images;

        $result = $this->touristDestinationInterface->createTouristDestination($inputData);

        if (!$result) {
            throw new JsonApiException(
                'Create tour destination information failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        if ($thumbnail) {
            $this->moveFiles($thumbnail, "tourist_destinations/temp", "tourist_destinations/{$result->id}");
        }
        if ($images) {
            $this->moveFiles($images, "tourist_destinations/temp", "tourist_destinations/{$result->id}");
        }

        return new TouristDestinationResponse(
            message: 'Create tour destination information successful',
            data: (new TouristDestinationResource($result))->resolve()
        );
    }

    private function handleUpdate($command): TouristDestinationResponse
    {

        $id = $command->id;
        $destinationExist = $this->touristDestinationInterface->findTouristDestinationById((int) $id);

        if (!$destinationExist) {
            throw new JsonApiException(
                'Tour destination is not available',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'place_name',
            'meta_title',
            'slug',
            'description',
            'details',
            'tour_group',
            'area',
            'status',
            'existing_thumbnail',
            'existing_images'
        ];
        $inputData = CommandDataHelper::extract($fields, $command);
        $existingImages = $inputData['existing_images'] ?? [];

        $currentImages = collect($destinationExist->images)->pluck('image')->toArray();

        $toDelete = array_diff($currentImages, $existingImages);
        foreach ($toDelete as $fileName) {
            $this->deleteFiles("tourist_destinations/{$id}/{$fileName}");
        }

        $thumbnailFiles = $command->request->file('thumbnail');
        $imageFiles = $command->request->file('images');

        $thumbnail = $thumbnailFiles ? $this->processFiles($thumbnailFiles, 'tourist_destinations/temp') : null;
        $newImages = $imageFiles ? $this->processFiles($imageFiles, 'tourist_destinations/temp') : null;

        if ($thumbnail) {
            $this->deleteFiles("tourist_destinations/{$id}/{$destinationExist->thumbnail}");
            $inputData['thumbnail'] = $thumbnail[0];
        } else {
            $inputData['thumbnail'] = $inputData['existing_thumbnail'] ?? null;
            if (!$inputData['thumbnail']) {
                $this->deleteFiles("tourist_destinations/{$id}/{$destinationExist->thumbnail}");
            }
        }

        $inputData['images'] = array_merge($newImages ?? [], $existingImages ?? []);

        $updated = $this->touristDestinationInterface->updateTouristDestination((int) $id, $inputData);

        if (!$updated) {
            throw new JsonApiException(
                'Tour destination information update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        if ($thumbnail) {
            $this->moveFiles($thumbnail, "tourist_destinations/temp", "tourist_destinations/{$id}");
        }

        if ($newImages) {
            $this->moveFiles($newImages, "tourist_destinations/temp", "tourist_destinations/{$id}");
        }

        return new TouristDestinationResponse(
            message: 'Tour destination information updated successfully',
            data: (new TouristDestinationResource($updated))->resolve()
        );
    }

    private function handleDelete($id): TouristDestinationResponse
    {

        $deleted = $this->touristDestinationInterface->deleteTouristDestination($id);

        if (!$deleted) {
            throw new JsonApiException(
                'Delete tour destination information failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $this->deleteFolders("tourist_destinations/$id");

        return new TouristDestinationResponse(
            message: 'Delete tour destination information successful'
        );
    }


    private function validateUnique($command, ?int $ignoreId = null)
    {
        $errorMess = [];

        $nameExist = $this->touristDestinationInterface->findTouristDestinationByName($command->place_name);
        $slugExist = $this->touristDestinationInterface->findTouristDestinationBySlug($command->slug);

        if ($nameExist && $nameExist->id !== $ignoreId) {
            $errorMess[] = 'Place name already exists';
        }

        if ($slugExist && $slugExist->id !== $ignoreId) {
            $errorMess[] = 'Slug already exists';
        }

        if ($errorMess) {
            throw new JsonApiException(
                implode(' & ', $errorMess),
                ResponseStatusCode::PARAMS_INVALID
            );
        }
    }
}
