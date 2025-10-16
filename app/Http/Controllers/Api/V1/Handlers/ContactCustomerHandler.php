<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Helper\CodeHelper;
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
        return match ($command->request->getMethod()) {
            'GET' => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command)
    {
        if (isset($command->id)) {
            return $this->handleGetContactById((int) $command->id);
        }

        return $this->handleFetchAll();
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


    private function handleCreate($command): ContactCustomerResponse
    {
        $fields = [
            'full_name',
            'email',
            'phone',
            'title',
            'contact_content',
            'contact_result',
            'status',
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        do {
            $contactCode = CodeHelper::generateCodeNumeric();
        } while (
            $this->contactCustomerInterface->findContactByCode($contactCode)
        );

        $inputData['contact_code'] = $contactCode;

        $result = $this->contactCustomerInterface->createContact($inputData);
        if (!$result) {
            throw new JsonApiException("Create contact customer failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return ContactCustomerResponse::from([
            'message' => "Create contact customer success",
            'data' => (new ContactCustomerResource($result))->resolve(),
        ]);
    }

    private function handleUpdate($command): ContactCustomerResponse
    {
        $id = $command->id;
        $existContact = $this->contactCustomerInterface->findContactById($id);
        if ($existContact->contact_code !== $command->contact_code) {
            throw new JsonApiException("Update contact customer failed", ResponseStatusCode::PARAMS_INVALID);
        }
        // dd($existContact);
        $fields = [
            'full_name',
            'email',
            'phone',
            'title',
            'contact_content',
            'contact_result',
            'status',
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        $result = $this->contactCustomerInterface->updateContact((int) $command->id, $inputData);

        if (!$result) {
            throw new JsonApiException("Update contact customer failed", ResponseStatusCode::PARAMS_INVALID);
        }

        return ContactCustomerResponse::from([
            'message' => "Update contact customer success",
            'data' => (new ContactCustomerResource($result))->resolve(),
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
