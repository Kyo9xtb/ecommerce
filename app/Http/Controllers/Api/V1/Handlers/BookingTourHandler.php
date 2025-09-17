<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\BookingTourResource;
use App\Http\Responses\Api\BookingTourResponse;
use App\Repositories\BookingTour\BookingTourInterface;

class BookingTourHandler
{

    public function __construct(
        public BookingTourInterface $bookingTourInterface,
    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();
        return match ($method) {
            'GET' => match (true) {
                isset($command->id)   => $this->handleGetBookingById($command->id),
                isset($command->date) => $this->handleGetBookingByDate($command->date),
                isset($command->month) => $this->handleGetBookingByMonth($command->month),
                default               => $this->handleFetchAllBooking(),
            },
            'POST'   => $this->handleWrite($command, 'createBookingTour', 'Create success'),
            'PUT'    => $this->handleWrite($command, 'updateBookingTour', 'Update success', $command->id),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleFetchAllBooking(): BookingTourResponse
    {
        $result = $this->bookingTourInterface->fetchAll();
        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return  BookingTourResponse::from(
            [
                'message' => "Success",
                'data' => BookingTourResource::collection($result)->toArray(request()),
            ]
        );
    }

    private function handleGetBookingById($id): BookingTourResponse
    {
        $result = $this->bookingTourInterface->findBookingTourById($id);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return BookingTourResponse::from(
            [
                'message' => "Success",
                'data' => (new BookingTourResource($result))->resolve()
            ]
        );
    }

    private function handleGetBookingByDate($date): BookingTourResponse
    {
        $result = $this->bookingTourInterface->filterBookingByDate($date);
        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return BookingTourResponse::from(
            [
                'message' => "Success",
                'data' => BookingTourResource::collection($result)->toArray(request())
            ]
        );
    }

    private function handleGetBookingByMonth($month): BookingTourResponse
    {
        $result = $this->bookingTourInterface->filterBookingByMonth($month);
        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return BookingTourResponse::from(
            [
                'message' => "Success",
                'data' => BookingTourResource::collection($result)->toArray(request())
            ]
        );
    }

    private function handleWrite($command, string $action, string $successMessage, $id = null): BookingTourResponse
    {
        $fields = [
            'user_id',
            'full_name',
            'email',
            'phone',
            'currency',
            'address',
            'note',
            'total_price',
            'deposit',
            'payment_method',
            'status',
            'details',
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        $data = $id
            ? $this->bookingTourInterface->{$action}((int) $id, $inputData)
            : $this->bookingTourInterface->{$action}($inputData);

        if (!$data) {
            throw new JsonApiException("{$successMessage} failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return BookingTourResponse::from([
            'message' => $successMessage,
            'data' => (new BookingTourResource($data))->resolve(),
        ]);
    }

    private function handleDelete($id): BookingTourResponse
    {

        $data = $this->bookingTourInterface->deleteBookingTour((int) $id);
        if (!$data) {
            throw new JsonApiException(
                'Delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return BookingTourResponse::from(
            [
                'message' => "Delete success",
            ]
        );
    }
}
