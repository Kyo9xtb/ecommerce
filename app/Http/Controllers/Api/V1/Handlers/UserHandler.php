<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Http\Responses\Api\UserResponse;
use App\Repositories\User\UserInterface;

class UserHandler
{

    public function __construct(
        public UserInterface $userInterface,
    ) {}

    public function handle($command)
    {
        $request = $command->request;
        $method = $request->getMethod();
        if ($method === 'GET') {
            return $this->getAll();
            // return $request->query->count()
            //     ? $this->getConfig($command->appKey, $command->accessType)
            //     : $this->getAll();
        }

        $handlers = [
            'POST'   => 'handlePost',
            'PUT'    => 'handlePut',
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

    private function getAll(): UserResponse
    {
        $data = $this->userInterface->getAllUser();
        // $result = $data->map(function ($item) {
        //     return $this->formatDataResult($item);
        // });
        // return ConfigVersionAccessResponse::from([
        //     'data' => $result->toArray(),
        //     'message' => 'Data fetched successfully'
        // ]);
        // dd($data);

        if (!$data) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return UserResponse::from(
            [
                'message' => "ssssssssss",
                'data' => $data->toArray(),
            ]
        );
    }
}
