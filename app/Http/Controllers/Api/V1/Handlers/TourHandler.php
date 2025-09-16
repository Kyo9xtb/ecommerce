<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Http\Resources\TourResource;
use App\Http\Responses\Api\TourResponse;
use App\Repositories\Tour\TourInterface;

class TourHandler
{

    public function __construct(
        public TourInterface $tourInterface,
    ) {}

    public function handle($command)
    {
        $request = $command->request;
        $method = $request->getMethod();
        $slug = $request->route('slug');
        if ($method === 'GET') {
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
            if ($command->id) {
                return $this->handleGetById($command->id);
            }
            return $this->handleGetAllTourActive();
        }

        $handlers = [
            'POST'   => 'handlePost',
            'PUT'    => 'handleUpdate',
            'DELETE' => 'handleDelete',
        ];

        if (!isset($handlers[$method])) {
            throw new JsonApiException(
                'Method not supported',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return $this->{$handlers[$method]}($command);
    }

    private function handleFetchAll(): TourResponse
    {
        $data = $this->tourInterface->fetchAll();

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: "Success",
            data: TourResource::collection($data)->toArray(request()),
        );
    }

    private function handleGetAllTourActive(): TourResponse
    {
        $data = $this->tourInterface->getAllTourActive();

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: "Success",
            data: TourResource::collection($data)->toArray(request()),
        );
    }

    private function handleGetBySlug($slug): TourResponse
    {
        $data = $this->tourInterface->findTourBySlug($slug);

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: "Success",
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleGetById($id): TourResponse
    {
        $data = $this->tourInterface->findTourById($id);

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return new TourResponse(
            message: "Success",
            data: (new TourResource($data))->resolve()
        );
    }

    private function handlePost($command): TourResponse
    {
        $isNameExist = $this->tourInterface->findTourName($command->tour_name);
        $isSlugExist = $this->tourInterface->findTourBySlug($command->slug);
        if ($isNameExist || $isSlugExist) {
            throw new JsonApiException(
                'Tour name or slug already exists',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $inputData = [
            'tour_name' => $command->tour_name,
            'slug' => $command->slug,
            'price' => $command->price,
            'sale' => $command->sale,
            'trip' => $command->trip,
            'time' => $command->time,
            'status' => $command->status,
            'tour_summary' => $command->tour_summary,
            'tour_program' => $command->tour_program,
            'tour_policy' => $command->tour_policy,
            'terms_conditions' => $command->terms_conditions,
            'images' => $command->images,
            'thumbnail' => $command->thumbnail,
            'area' => $command->area,
            'tour_group' => $command->tour_group,
            'vehicles' => $command->vehicles,
        ];
        
        $data = $this->tourInterface->createTour($inputData);

        if (!$data) {
            throw new JsonApiException(
                'Creation failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourResponse(
            message: "Creation successful",
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleUpdate($command): TourResponse
    {
        $isNameExist = $this->tourInterface->findTourName($command->tour_name);
        $isSlugExist = $this->tourInterface->findTourBySlug($command->slug);
        if (
            ($isNameExist && $isNameExist->id !== $command->id) ||
            ($isSlugExist && $isSlugExist->id !== $command->id)

        ) {
            throw new JsonApiException(
                'Tour name or slug already exists',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $inputData = [
            'tour_name' => $command->tour_name,
            'slug' => $command->slug,
            'price' => $command->price,
            'sale' => $command->sale,
            'trip' => $command->trip,
            'time' => $command->time,
            'status' => $command->status,
            'tour_summary' => $command->tour_summary,
            'tour_program' => $command->tour_program,
            'tour_policy' => $command->tour_policy,
            'terms_conditions' => $command->terms_conditions,
            'images' => $command->images,
            'thumbnail' => $command->thumbnail,
            'area' => $command->area,
            'tour_group' => $command->tour_group,
            'vehicles' => $command->vehicles,
        ];

        $data = $this->tourInterface->updateTour((int) $command->id, $inputData);
        if (!$data) {
            throw new JsonApiException(
                'Update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourResponse(
            message: "Update successful",
            data: (new TourResource($data))->resolve()
        );
    }

    private function handleDelete($command): TourResponse
    {

        $data = $this->tourInterface->deleteTour((int) $command->id);
        if (!$data) {
            throw new JsonApiException(
                'Delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourResponse(
            message: "Delete successful",
        );
    }
}
