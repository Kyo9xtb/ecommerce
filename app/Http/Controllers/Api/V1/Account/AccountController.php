<?php

namespace App\Http\Controllers\Api\V1\Account;


use App\Http\Controllers\Api\V1\Account\Commands\CustomerAuthCommand;
use App\Http\Controllers\Api\V1\Account\Handlers\CustomerAuthHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CustomerAuthRequest;
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
}
