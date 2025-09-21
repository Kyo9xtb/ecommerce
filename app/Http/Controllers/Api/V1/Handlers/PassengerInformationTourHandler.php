<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\PassengerInformationTourResource;
use App\Http\Responses\Api\PassengerInformationTourResponse;
use App\Repositories\BookingTour\BookingTourInterface;
use App\Repositories\PassengerInformationTour\PassengerInformationTourInterface;
use App\Repositories\Tour\TourInterface;

class PassengerInformationTourHandler
{

    public function __construct(
        public PassengerInformationTourInterface $passengerInformationTourInterface,
        public BookingTourInterface $bookingTourInterface,
        public TourInterface $tourInterface,
    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();
        return match ($method) {
            'GET' => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command): PassengerInformationTourResponse
    {
        return  $this->handleGetPassengersBookingTour(1, 2);

        if ($command->booking_id && $command->tour_id) {
            return  $this->handleGetPassengersBookingTour($command->booking_id, $command->tour_id);
        }

        throw new JsonApiException(
            'No data found',
            ResponseStatusCode::PARAMS_INVALID
        );
    }

    private function handleGetPassengersBookingTour($bookingId, $tourId): PassengerInformationTourResponse
    {
        $result = $this->passengerInformationTourInterface->filterPassengersBookingTour($bookingId, $tourId);

        if (empty($result)) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return PassengerInformationTourResponse::from(
            [
                'message' => 'Success',
                'data' => PassengerInformationTourResource::collection($result)->toArray(request()),
            ]
        );
    }

    private function handleCreate($command): PassengerInformationTourResponse
    {

        $errorMess = [];
        $bookingExist = $this->bookingTourInterface->findBookingTourById((int) $command->booking_id);
        $tourExist = $this->tourInterface->findTourById((int) $command->tour_id);

        if (!$bookingExist) {
            $errorMess[] = 'Booking does not exist.';
        }

        if (!$tourExist) {
            $errorMess[] = 'Tour does not exist.';
        }

        if ($errorMess) {
            throw new JsonApiException(
                implode(' & ', $errorMess),
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'booking_id',
            'tour_id',
            'customer',
            'full_name',
            'birthday',
            'gender',
            'card_id'
        ];

        $inputData = array_filter(
            CommandDataHelper::extract($fields, $command),
            fn($value) => !is_null($value)
        );
        $inputData['birthday'] = (new \DateTime($inputData['birthday']))->format('Y-m-d H:i:s');

        $result =  $this->passengerInformationTourInterface->createPassenger($inputData);

        if (empty($result)) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new PassengerInformationTourResponse(
            message: "Success",
            data: (new PassengerInformationTourResource($result))->resolve(),
        );
    }

    private function handleUpdate($command)
    {
        $errorMess = [];
        $id = $command->id;
        $passengerExist = $this->passengerInformationTourInterface->findPassengerById($id);

        if (!$passengerExist) {
            throw new JsonApiException(
                'No passenger information available',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        if ($passengerExist->booking_id != $command->booking_id) {
            $errorMess[] = 'Booking id has been changed.';
        }

        if ($passengerExist->tour_id != $command->tour_id) {
            $errorMess[] = 'Tour id has been changed.';
        }

        if ($errorMess) {
            throw new JsonApiException(
                implode(' & ', $errorMess),
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'customer',
            'full_name',
            'birthday',
            'gender',
            'card_id'
        ];
        $inputData = CommandDataHelper::extract($fields, $command);
        $inputData['birthday'] = (new \DateTime($inputData['birthday']))->format('Y-m-d H:i:s');

        $result =  $this->passengerInformationTourInterface->updatePassenger($id, $inputData);

        if (empty($result)) {
            throw new JsonApiException(
                'Passenger information update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return PassengerInformationTourResponse::from(
            [
                'message' => 'Passenger information updated successfully',
                'data' => (new PassengerInformationTourResource($result))->resolve()
            ]
        );
    }
    private function handleDelete($id)
    {
        $result = $this->passengerInformationTourInterface->deletePassenger($id);

        if (empty($result)) {
            throw new JsonApiException(
                'Passenger information delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return PassengerInformationTourResponse::from(
            [
                'message' => 'Passenger information deleted successfully',
            ]
        );
    }
}
