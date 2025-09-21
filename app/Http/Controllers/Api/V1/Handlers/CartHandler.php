<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\CartResource;
use App\Http\Responses\Api\CartResponse;
use App\Repositories\Cart\CartInterface;
use App\Repositories\Tour\TourInterface;
use App\Repositories\User\UserInterface;

class CartHandler
{

    public function __construct(
        public CartInterface $cartInterface,
        public UserInterface $userInterface,
        public TourInterface $tourInterface,

    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();
        return match ($method) {
            'GET' => $this->handleGetCartByUserId((int) $command->user_id),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete($command),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGetCartByUserId($id): CartResponse
    {
        if (!$id) {
            throw new JsonApiException(
                'Missing input parameter.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        $userExist = $this->userInterface->findUserActive((int) $id);

        if (!$userExist) {
            throw new JsonApiException(
                'No data found.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $result = $this->cartInterface->getCartByUserId($id);

        if (!$result) {
            throw new JsonApiException(
                'No data found.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return CartResponse::from(
            [
                'message' => 'Success.',
                'data' => CartResource::collection($result)->toArray(request()),
            ]
        );
    }

    private function handleCreate($command)
    {
        $errorMess = [];
        $userExist = $this->userInterface->findUserActive((int) $command->user_id);
        $tourExist = $this->tourInterface->findTourById((int) $command->tour_id);

        if (!$userExist) {
            $errorMess[] = 'User does not exist.';
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
            'user_id',
            'tour_id',
            'customer',
            'quantity',
            'price',
        ];

        $inputData = array_filter(
            CommandDataHelper::extract($fields, $command),
            fn($value) => !is_null($value)
        );

        $result = $this->cartInterface->createCart($inputData);

        if (!$result) {
            throw new JsonApiException(
                'Create cart failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return CartResponse::from(
            [
                'message' => 'Create cart success.',
                'data' => (new CartResource($result))->resolve(),

            ]
        );
    }

    private function handleUpdate($command)
    {
        $fields = [
            'user_id',
            'tour_id',
            'customer',
            'quantity',
            'price',
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        $result = $this->cartInterface->updateCart($command->id, $inputData);

        if (!$result) {
            throw new JsonApiException(
                'Update cart failed.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return CartResponse::from(
            [
                'message' => 'Update cart success.',
                'data' => (new CartResource($result))->resolve(),
            ]
        );
    }

    private function handleDelete($command)
    {
        if ($command->clear_all && $command->user_id) {
            return $this->handleDeleteAll((int)$command->user_id);
        }
        if ($command->id) {
            return $this->handleDeleteById((int)$command->id);
        }

        throw new JsonApiException(
            'Delete cart failed. Missing input parameter.',
            ResponseStatusCode::PARAMS_INVALID
        );
    }
    private function handleDeleteById($id)
    {
        $result = $this->cartInterface->deleteCart($id);

        if (!$result) {
            throw new JsonApiException(
                'Delete cart failed.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return CartResponse::from(
            [
                'message' => 'Delete cart success.',
            ]
        );
    }
    private function handleDeleteAll($id)
    {
        $result = $this->cartInterface->deleteAllCartByUserId($id);

        if (!$result) {
            throw new JsonApiException(
                'Delete cart failed.',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return CartResponse::from(
            [
                'message' => 'Delete cart success.',
            ]
        );
    }
}
