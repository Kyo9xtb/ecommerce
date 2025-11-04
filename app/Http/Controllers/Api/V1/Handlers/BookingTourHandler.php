<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CodeHelper;
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
            'GET' => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete((int) $command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command)
    {
        if (isset($command->id)) {
            return $this->handleGetBookingById((int) $command->id);
        }

        if (isset($command->date)) {
            return $this->handleGetBookingByDate($command->date);
        }

        if (isset($command->month)) {
            return $this->handleGetBookingByMonth($command->month);
        }

        return $this->handleFetchAllBooking();
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

        // $data = $this->bookingTourInterface->updateBookingTour((int) $id, $inputData);
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


    private function handleCreate($command)
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

        $inputData = array_filter(CommandDataHelper::extract($fields, $command), fn($v) => !is_null($v));

        do {
            $prefix = !empty($inputData['user_id']) ? 'MEM' : 'IND';

            $bookingCode = CodeHelper::generateCodeNumeric($prefix);
        } while ($this->bookingTourInterface->findBookingTourByCode($bookingCode));

        $inputData['booking_code'] = $bookingCode;

        $data = $this->bookingTourInterface->createBookingTour($inputData);

        if (!$data) {
            throw new JsonApiException("Create booking tour failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return BookingTourResponse::from([
            'message' => 'Create booking tour success',
            'data' => (new BookingTourResource($data))->resolve(),
        ]);
    }

    private function handleUpdate($command)
    {
        $idReq = (int) $command->id;
        $existBooking = $this->bookingTourInterface->find($idReq);

        if (!$existBooking) {
            throw new JsonApiException('No tour booking information available.', ResponseStatusCode::PARAMS_INVALID);
        }

        if ($existBooking->booking_code !== $command->booking_code) {
            throw new JsonApiException('The tour code has been changed.', ResponseStatusCode::PARAMS_INVALID);
        }

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

        $data = $this->bookingTourInterface->updateBookingTour($idReq, $inputData);

        if (!$data) {
            throw new JsonApiException("Update booking tour failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return BookingTourResponse::from([
            'message' => 'Update booking tour success',
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
