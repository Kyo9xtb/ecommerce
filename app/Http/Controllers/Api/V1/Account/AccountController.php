<?php

namespace App\Http\Controllers\Api\V1\Account;


use App\Http\Controllers\Api\V1\Account\Commands\CustomerAuthCommand;
use App\Http\Controllers\Api\V1\Account\Commands\EmployeeAuthCommand;
use App\Http\Controllers\Api\V1\Account\Handlers\CustomerAuthHandler;
use App\Http\Controllers\Api\V1\Account\Handlers\EmployeeAuthHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CustomerAuthRequest;
use App\Http\Requests\Account\EmployeeAuthRequest;
use Joselfonseca\LaravelTactician\CommandBusInterface;

class AccountController extends Controller
{
    public function __construct(public CommandBusInterface $commandBus) {}


    public function CustomerAuth(CustomerAuthRequest $request)
    {
        $this->commandBus->addHandler(CustomerAuthCommand::class, CustomerAuthHandler::class);

        $command = $this->commandBus->dispatch(new CustomerAuthCommand($request));

        return $command->original ?? $command;
    }

    public function EmployeeAuth(EmployeeAuthRequest $request)
    {
        $this->commandBus->addHandler(EmployeeAuthCommand::class, EmployeeAuthHandler::class);

        $command = $this->commandBus->dispatch(new EmployeeAuthCommand($request));

        return $command;
    }
}
