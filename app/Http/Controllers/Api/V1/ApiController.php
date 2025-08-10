<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Commands\ApiCommand;
use App\Http\Controllers\Api\V1\Commands\UserCommand;
use App\Http\Controllers\Api\V1\Handlers\ApiHandler;
use App\Http\Controllers\Api\V1\Handlers\UserHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiRequest;
use App\Http\Requests\Api\UserRequest;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class ApiController extends Controller
{
    public function __construct(public CommandBusInterface $commandBus) {}


    public function demo1(ApiRequest $request)
    {
        $this->commandBus->addHandler(ApiCommand::class, ApiHandler::class);
        $demo1 = new ApiCommand($request);

        return $this->responseSuccess($this->commandBus->dispatch($demo1));
    }

    public function user(UserRequest $request)
    {
        $this->commandBus->addHandler(UserCommand::class, UserHandler::class);

        $user = $this->commandBus->dispatch(new UserCommand($request));

        return $this->responseSuccess($user->data, $user->message);
    }
}
