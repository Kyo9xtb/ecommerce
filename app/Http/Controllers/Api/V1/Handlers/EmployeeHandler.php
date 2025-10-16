<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CodeHelper;
use App\Helper\CommandDataHelper;
use App\Http\Resources\EmployeeResource;
use App\Http\Responses\Api\EmployeeResponse;
use App\Http\Responses\Api\UserResponse;
use App\Repositories\Employee\EmployeeInterface;


class EmployeeHandler
{

    public function __construct(
        public EmployeeInterface $employeeInterface,
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
            return $this->getEmployeeById((int) $command->id);
        }
        return $this->getAll();
    }

    private function getAll(): UserResponse
    {
        $result = $this->employeeInterface->getAllEmployee();

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return UserResponse::from(
            [
                'message' => 'Get Employee information successfully',
                'data' => EmployeeResource::collection($result)->toArray(request()),
            ]
        );
    }

    private function getEmployeeById($id): UserResponse
    {
        $result = $this->employeeInterface->findEmployeeById($id);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return UserResponse::from(
            [
                'message' => 'Get Employee information successfully',
                'data' => (new EmployeeResource($result))->resolve(),
            ]
        );
    }

    private function handlePost($command)
    {
        $emailExist = $this->employeeInterface->findEmployeeByEmail($command->email);

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
            'position',
            'department',
            'status',
            'password'
        ];

        $inputData = array_filter(CommandDataHelper::extract($fields, $command), fn($v) => !is_null($v));

        do {
            $employeeCode = CodeHelper::generateCodeNumeric('', 5);
        } while (
            $this->employeeInterface->findEmployeeByCode($employeeCode)
        );

        $inputData['employee_code'] = $employeeCode;

        $result = $this->employeeInterface->createEmployee($inputData);
        if (!$result) {
            throw new JsonApiException('Create Employee failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return EmployeeResponse::from([
            'message' => 'Create contact Employee success',
            'data' => (new EmployeeResource($result))->resolve(),
        ]);
    }

    private function handlePut($command): EmployeeResponse
    {
        $errors = [];
        $id = $command->id;
        $employeeExist = $this->employeeInterface->findEmployeeById($id);

        if (!$employeeExist) {
            throw new JsonApiException('The Employee does not exist.', ResponseStatusCode::PARAMS_INVALID);
        }

        if ($employeeExist->email !== $command->email) {
            $errors[] = 'Email has been changed';
        }

        if ($employeeExist->employee_code !== $command->employee_code) {
            $errors[] = 'The Employee code has been changed.';
        }

        if ($errors) {
            throw new JsonApiException(implode(' & ', $errors), ResponseStatusCode::PARAMS_INVALID);
        }
         $fields = [
            'full_name',
            'birth_date',
            'gender',
            'email',
            'phone',
            'address',
            'position',
            'department',
            'status',
        ];
        $inputData = CommandDataHelper::extract($fields, $command);

        $result = $this->employeeInterface->updateEmployee($id, $inputData);

        if (!$result) {
            throw new JsonApiException('Update Employee failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return EmployeeResponse::from([
            'message' => 'Update Employee success',
            'data' => (new EmployeeResource($result))->resolve(),
        ]);
    }

    private function handleDelete($id): EmployeeResponse
    {
        $employeeExist = $this->employeeInterface->findEmployeeById($id);

        if (!$employeeExist) {
            throw new JsonApiException('The Employee does not exist.', ResponseStatusCode::PARAMS_INVALID);
        }

        $result = $this->employeeInterface->deleteEmployee($id);

        if (!$result) {
            throw new JsonApiException('Delete Employee failed', ResponseStatusCode::PARAMS_INVALID);
        }

        return EmployeeResponse::from([
            'message' => 'Delete Employee success',
        ]);
    }
}
