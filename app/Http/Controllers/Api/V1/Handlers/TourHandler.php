<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CodeHelper;
use App\Helper\CommandDataHelper;
use App\Http\Resources\TourResource;
use App\Http\Responses\Api\TourResponse;
use App\Repositories\Tour\TourInterface;
use Illuminate\Support\Facades\Storage;

class TourHandler
{

    public function __construct(
        public TourInterface $tourInterface,
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
        $slug = $command->request->route('slug');
        if ($slug) {
            switch ($slug) {
                case 'all-tour':
                    return $this->handleFetchAll();
                case 'active':
                    return $this->handleGetAllTourActive();
                default:
                    return $this->handleGetBySlug($slug);
            }
        }

        return $this->handleFetchAll();
    }

    private function handleFetchAll(): TourResponse
    {
        $data = $this->tourInterface->fetchAll();

        if (!$data) {
            throw new JsonApiException(
                'Failed to get tour information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: 'Get tour information successfully',
            data: TourResource::collection($data)->toArray(request()),
        );
    }

    private function handleGetAllTourActive(): TourResponse
    {
        $data = $this->tourInterface->getAllTourActive();

        if (!$data) {
            throw new JsonApiException(
                'Failed to get tour information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: 'Get tour information successfully',
            data: TourResource::collection($data)->toArray(request()),
        );
    }

    private function handleGetBySlug($slug): TourResponse
    {
        $data = $this->tourInterface->findTourBySlug($slug);

        if (!$data) {
            throw new JsonApiException(
                'Failed to get tour information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: 'Get tour information successfully',
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleGetById($id): TourResponse
    {
        $data = $this->tourInterface->findTourById($id);

        if (!$data) {
            throw new JsonApiException(
                'Failed to get tour information',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: 'Get tour information successfully',
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleCreate($command): TourResponse
    {
        $errorMess = [];

        $isNameExist = $this->tourInterface->findTourName($command->tour_name);
        $isSlugExist = $this->tourInterface->findTourBySlug($command->slug);

        if ($isNameExist) {
            $errorMess[] = 'Tour name already exists';
        }

        if ($isSlugExist) {
            $errorMess[] = 'Slug already exists';
        }

        if ($errorMess) {
            throw new JsonApiException(
                implode(' & ', $errorMess),
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'tour_name',
            'slug',
            'price',
            'sale',
            'trip',
            'time',
            'status',
            'tour_summary',
            'tour_program',
            'tour_policy',
            'terms_conditions',
            'area',
            'tour_group',
            'departure_schedule',
            'vehicles',
            'guests',
        ];
        extract($this->processFiles($command));
        $inputData = array_filter(
            CommandDataHelper::extract($fields, $command),
            fn($value) => !is_null($value)
        );

        $prefix = match ((int) ($inputData['tour_group'] ?? 0)) {
            1 => 'DOM',  // Domestic
            2 => 'INT',  // International
            3 => 'TMB',  // Team Building
            4 => 'OTH',  // Others
            default => 'UNDEF', // Undefined
        };
        do {
            $tourCode = CodeHelper::generate("TOUR-$prefix");
        } while (
            $this->tourInterface->findTourCode($tourCode)
        );

        $inputData['tour_code'] =  $tourCode;
        $inputData['thumbnail'] =  $thumbnail;
        $inputData['images'] = $images;

        $data = $this->tourInterface->createTour($inputData);

        $this->moveFiles($data->id, $thumbnail, $images);

        if (!$data) {
            throw new JsonApiException(
                'Create tour information failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourResponse(
            message: 'Create tour information successful',
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleUpdate($command): TourResponse
    {

        $id = $command->id;
        $errorMess = [];
        $tourExist = $this->tourInterface->findTourById((int) $id);

        if (!$tourExist) {
            throw new JsonApiException(
                'Tour information is not available',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        if ($tourExist->tour_code !== $command->tour_code) {
            throw new JsonApiException(
                'The tour code has been changed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $isNameExist = $this->tourInterface->findTourName($command->tour_name);
        $isSlugExist = $this->tourInterface->findTourBySlug($command->slug);

        if ($isNameExist && $isNameExist->id != $id) {
            $errorMess[] = 'Tour name already exists';
        }
        if ($isSlugExist && $isSlugExist->id != $id) {
            $errorMess[] = 'Slug already exists';
        }

        if ($errorMess) {
            throw new JsonApiException(
                implode(' & ', $errorMess),
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $tourOld = $this->tourInterface->findTourById($id);

        if ($command->request->hasFile('thumbnail') && $tourOld->thumbnail) {
            Storage::disk('public')->delete("tour/{$id}/{$tourOld->thumbnail}");
        }

        if ($command->request->hasFile('images') && !empty($tourOld->images)) {
            foreach ($tourOld->images as $image) {
                Storage::disk('public')->delete("tour/{$id}/{$image->image}");
            }
        }
        if (!$command->thumbnail) {
            Storage::disk('public')->delete("tour/{$id}/{$tourOld->thumbnail}");
        }

        if (!$command->images) {
            foreach ($tourOld->images as $image) {
                Storage::disk('public')->delete("tour/{$id}/{$image->image}");
            }
        }

        extract($this->processFiles($command));

        $fields = [
            'tour_name',
            'slug',
            'price',
            'sale',
            'trip',
            'time',
            'status',
            'tour_summary',
            'tour_program',
            'tour_policy',
            'terms_conditions',
            'area',
            'tour_group',
            'vehicles',
            'departure_schedule',
            'guests',
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        if ($command->request->hasFile('thumbnail')) {
            $inputData['thumbnail'] = $thumbnail;
        }

        if ($command->request->hasFile('images')) {
            $inputData['images'] = $images;
        }

        $data = $this->tourInterface->updateTour((int) $id, $inputData);

        $this->moveFiles($id, $thumbnail, $images);

        if (!$data) {
            throw new JsonApiException(
                'Tour information update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourResponse(
            message: 'Tour information updated successfully',
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleDelete($id): TourResponse
    {

        $deleted = $this->tourInterface->deleteTour($id);

        if (!$deleted) {
            throw new JsonApiException(
                'Delete tour information failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $directory = "tour/$id";
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        return new TourResponse(
            message: 'Delete tour information successful'
        );
    }

    private function processFiles($command): array
    {
        $images = [];
        $thumbnail = null;

        if ($command->request->hasFile('thumbnail')) {
            $filename = time() . '.' . $command->request->file('thumbnail')->getClientOriginalExtension();
            $command->request->file('thumbnail')->storeAs('public/tour', $filename);
            $thumbnail = $filename;
        }

        if ($command->request->hasFile('images')) {
            foreach ($command->request->file('images') as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/tour', $filename);
                $images[] = $filename;
            }
        }

        return compact('thumbnail', 'images');
    }

    private function moveFiles(int $tourId, ?string $thumbnail, array $images): void
    {
        if ($thumbnail) {
            Storage::disk('public')->move("tour/$thumbnail", "tour/$tourId/$thumbnail");
        }

        foreach ($images as $img) {
            Storage::disk('public')->move("tour/$img", "tour/$tourId/$img");
        }
    }

    function deleteOldFiles($tour, $id)
    {
        if ($tour->thumbnail) {
            Storage::disk('public')->delete("tour/{$id}/{$tour->thumbnail}");
        }

        foreach ($tour->images ?? [] as $image) {
            Storage::disk('public')->delete("tour/{$id}/{$image}");
        }
    }
}
