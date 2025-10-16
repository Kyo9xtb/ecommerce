<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CodeHelper;
use App\Helper\CommandDataHelper;
use App\Http\Resources\CustomerResource;
use App\Http\Responses\Api\CustomerResponse;
use App\Http\Responses\Api\UserResponse;
use App\Repositories\Customer\CustomerInterface;


class CustomerHandler
{

    public function __construct(
        public CustomerInterface $customerInterface,
    ) {}

    public function handle($command)
    {

        return match ($command->request->getMethod()) {
            'GET' => $this->handleGet($command),
            'POST' => $this->handlePost($command),
            'PUT' => $this->handlePut($command),
            'DELETE' => $this->handleDelete((int)$command->id),
            default => throw new JsonApiException(
                'Method not supported',
                ResponseStatusCode::PARAMS_INVALID
            )
        };
    }

    private function handleGet($command)
    {
        if ($command->id) {
            return $this->getCustomerById((int) $command->id);
        }
        return $this->getAll();
    }

    private function getAll(): UserResponse
    {
        $result = $this->customerInterface->getAllCustomer();

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return UserResponse::from(
            [
                'message' => 'Get customer information successfully',
                'data' => CustomerResource::collection($result)->toArray(request()),
            ]
        );
    }

    private function getCustomerById($id): UserResponse
    {
        $result = $this->customerInterface->findCustomerById($id);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return UserResponse::from(
            [
                'message' => 'Get customer information successfully',
                'data' => (new CustomerResource($result))->resolve(),
            ]
        );
    }

    private function handlePost($command): CustomerResponse
    {
        $emailExist = $this->customerInterface->findCustomerByEmail($command->email);

        if ($emailExist) {
            throw new JsonApiException('Email already exists', ResponseStatusCode::PARAMS_INVALID);
        }

        $fields = [
            'full_name',
            'birth_date',
            'gender',
            'email',
            'phone',
            'address',
            'card_id',
        ];

        $inputData = array_filter(CommandDataHelper::extract($fields, $command), fn($v) => !is_null($v));

        do {
            $customerCode = CodeHelper::generateCodeNumeric('', 5);
        } while (
            $this->customerInterface->findCustomerByCode($customerCode)
        );
        
        $inputData['customer_code'] = $customerCode;

        $result = $this->customerInterface->createCustomer($inputData);
        if (!$result) {
            throw new JsonApiException('Create customer failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return CustomerResponse::from([
            'message' => 'Create contact customer success',
            'data' => (new CustomerResource($result))->resolve(),
        ]);
    }

    private function handlePut($command): CustomerResponse
    {
        $errors = [];
        $id = $command->id;
        $customerExist = $this->customerInterface->findCustomerById($id);

        if (!$customerExist) {
            throw new JsonApiException('The customer does not exist.', ResponseStatusCode::PARAMS_INVALID);
        }

        if ($customerExist->email !== $command->email) {
            $errors[] = 'Email has been changed';
        }

        if ($customerExist->customer_code !== $command->customer_code) {
            $errors[] = 'The customer code has been changed.';
        }

        if ($errors) {
            throw new JsonApiException(implode(' & ', $errors), ResponseStatusCode::PARAMS_INVALID);
        }
        $fields = [
            'full_name',
            'birth_date',
            'gender',
            'phone',
            'address',
            'card_id',
            'status'
        ];
        $inputData = CommandDataHelper::extract($fields, $command);

        $result = $this->customerInterface->updateCustomer($id, $inputData);
        
        if (!$result) {
            throw new JsonApiException('Update customer failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return CustomerResponse::from([
            'message' => 'Update customer success',
            'data' => (new CustomerResource($result))->resolve(),
        ]);
    }

    private function handleDelete($id): CustomerResponse
    {
        $customerExist = $this->customerInterface->findCustomerById($id);

        if (!$customerExist) {
            throw new JsonApiException('The customer does not exist.', ResponseStatusCode::PARAMS_INVALID);
        }

        $result = $this->customerInterface->deleteCustomer($id);

        if (!$result) {
            throw new JsonApiException('Delete customer failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return CustomerResponse::from([
            'message' => 'Delete customer success',
        ]);
    }
}
