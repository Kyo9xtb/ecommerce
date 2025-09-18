<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\ContactCustomerResource;
use App\Http\Responses\Api\ContactCustomerResponse;
use App\Repositories\ContactCustomer\ContactCustomerInterface;

class ContactCustomerHandler
{

    public function __construct(
        public ContactCustomerInterface $contactCustomerInterface,
    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();
        return match ($method) {
            'GET' => match (true) {
                isset($command->id)   => $this->handleGetContactById($command->id),
                default               => $this->handleFetchAll(),
            },
            'POST'   => $this->handleWrite($command, 'createContact', 'Create'),
            'PUT'    => $this->handleWrite($command, 'updateContact', 'Update', $command->id),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleFetchAll(): ContactCustomerResponse
    {
        $result = $this->contactCustomerInterface->fetchAll();
        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return  ContactCustomerResponse::from(
            [
                'message' => "Success",
                'data' => ContactCustomerResource::collection($result)->toArray(request()),

            ]
        );
    }

    private function handleGetContactById($id): ContactCustomerResponse
    {
        $result = $this->contactCustomerInterface->findContactById($id);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        return ContactCustomerResponse::from(
            [
                'message' => "Success",
                'data' => (new ContactCustomerResource($result))->resolve()
            ]
        );
    }


    private function handleWrite($command, string $action, string $successMessage, $id = null): ContactCustomerResponse
    {
        $fields = [
            'full_name',
            'email',
            'phone',
            'contact_content',
            'contact_result',
            'status',
            'title'
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        $data = $id
            ? $this->contactCustomerInterface->{$action}((int) $id, $inputData)
            : $this->contactCustomerInterface->{$action}($inputData);

        if (!$data) {
            throw new JsonApiException("{$successMessage} failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return ContactCustomerResponse::from([
            'message' => "{$successMessage} success",
            'data' => (new ContactCustomerResource($data))->resolve(),
        ]);
    }

    private function handleDelete($id): ContactCustomerResponse
    {

        $data = $this->contactCustomerInterface->deleteContact((int) $id);
        if (!$data) {
            throw new JsonApiException(
                'Delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return ContactCustomerResponse::from(
            [
                'message' => "Delete success",
            ]
        );
    }
}
