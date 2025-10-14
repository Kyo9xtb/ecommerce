<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Helper\CodeHelper;
use App\Http\Resources\TourRequireResource;
use App\Http\Responses\Api\TourRequireResponse;
use App\Repositories\TourRequire\TourRequireInterface;

class TourRequireHandler
{

    public function __construct(
        public TourRequireInterface $tourRequireInterface,
    ) {}

    public function handle($command)
    {
        return match ($command->request->getMethod()) {
            'GET'    => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete((int) $command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command)
    {
        if (isset($command->id)) {
            return $this->handleGetById((int) $command->id);
        }

        return $this->handleFetchAll();
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

    private function handleGetById(int $id): TourRequireResponse
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

    private function handleCreate($command): TourRequireResponse
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
            'price',
            'tour_program',
            'tour_policy',
            'terms_conditions'
        ];
        $inputData = array_filter(CommandDataHelper::extract($fields, $command), fn($v) => !is_null($v));

        do {
            $tourCode = CodeHelper::generate('TR');
        } while (
            $this->tourRequireInterface->findTourRequireTourCode($tourCode)
        );

        $inputData['tour_code'] = $tourCode;
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
            'price',
            'tour_program',
            'tour_policy',
            'terms_conditions'
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

    private function handleDelete(int $id): TourRequireResponse
    {
        $data = $this->tourRequireInterface->deleteTourRequire($id);
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
