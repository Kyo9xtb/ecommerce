<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\TourRequireResource;
use App\Http\Responses\Api\TourRequireResponse;
use App\Models\TourRequire;
use App\Repositories\TourRequire\TourRequireInterface;

class TourRequireHandler
{

    public function __construct(
        public TourRequireInterface $tourRequireInterface,
    ) {}

    public function handle($command)
    {
        $request = $command->request;
        $method = $request->getMethod();
        $slug = $request->route('slug');
        if ($method === 'GET') {
            if ($command->id) {
                return $this->handleGetById($command->id);
            }
            return $this->handleFetchAll();
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

    private function handleFetchAll(): TourRequireResponse
    {
        $data = $this->tourRequireInterface->fetchAllTourRequire();

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return TourRequireResponse::from([
            'message' => 'Success',
            'data' => TourRequireResource::collection($data)->toArray(request()),
        ]);
    }

    private function handleGetById($id): TourRequireResponse
    {
        $data = $this->tourRequireInterface->findTourRequireById($id);

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return TourRequireResponse::from(
            [
                'message' => "Success",
                'data' => (new TourRequireResource($data))->resolve()
            ]
        );
    }

    private function handlePost($command): TourRequireResponse
    {
        $fields = [
            'full_name',
            'nationality',
            'email',
            'phone',
            'expected_destination',
            'departure_date',
            'status',
            'end_date',
            'expected_month',
            'expected_year',
            'number_days',
            'vehicle',
            'adult',
            'children',
            'baby',
            'number_rooms',
            'hotel_standards',
            'note',
            'feedback',
        ];
        $inputData = CommandDataHelper::extract($fields, $command);
        $data = $this->tourRequireInterface->createTourRequire($inputData);

        if (!$data) {
            throw new JsonApiException(
                'Creation failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return TourRequireResponse::from(
            [
                'message' => "Creation successful",
                'data' => (new TourRequireResource($data))->resolve()
            ]
        );
    }

    private function handleUpdate($command): TourRequireResponse
    {
        $fields = [
            'full_name',
            'nationality',
            'email',
            'phone',
            'expected_destination',
            'departure_date',
            'status',
            'end_date',
            'expected_month',
            'expected_year',
            'number_days',
            'vehicle',
            'adult',
            'children',
            'baby',
            'number_rooms',
            'hotel_standards',
            'note',
            'feedback',
        ];
        $inputData = CommandDataHelper::extract($fields, $command);

        $data = $this->tourRequireInterface->updateTourRequire((int) $command->id, $inputData);
        if (!$data) {
            throw new JsonApiException(
                'Update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new TourRequireResponse(
            message: "Update successful",
            data: (new TourRequireResource($data))->resolve()
        );
    }

    private function handleDelete($command): TourRequireResponse
    {

        $data = $this->tourRequireInterface->deleteTourRequire((int) $command->id);
        if (!$data) {
            throw new JsonApiException(
                'Delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return TourRequireResponse::from(
            [
                'message' => "Delete successful",
            ]
        );
    }
}
