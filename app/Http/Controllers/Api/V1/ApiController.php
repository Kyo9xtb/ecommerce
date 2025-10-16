<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Commands\ApiCommand;
use App\Http\Controllers\Api\V1\Commands\BookingTourCommand;
use App\Http\Controllers\Api\V1\Commands\CartCommand;
use App\Http\Controllers\Api\V1\Commands\ContactCustomerCommand;
use App\Http\Controllers\Api\V1\Commands\CustomerCommand;
use App\Http\Controllers\Api\V1\Commands\NewsCommand;
use App\Http\Controllers\Api\V1\Commands\PassengerInformationTourCommand;
use App\Http\Controllers\Api\V1\Commands\TourCommand;
use App\Http\Controllers\Api\V1\Commands\TouristDestinationCommand;
use App\Http\Controllers\Api\V1\Commands\TourRequireCommand;
use App\Http\Controllers\Api\V1\Commands\UserCommand;
use App\Http\Controllers\Api\V1\Handlers\ApiHandler;
use App\Http\Controllers\Api\V1\Handlers\BookingTourHandler;
use App\Http\Controllers\Api\V1\Handlers\CartHandler;
use App\Http\Controllers\Api\V1\Handlers\ContactCustomerHandler;
use App\Http\Controllers\Api\V1\Handlers\CustomerHandler;
use App\Http\Controllers\Api\V1\Handlers\NewsHandler;
use App\Http\Controllers\Api\V1\Handlers\PassengerInformationTourHandler;
use App\Http\Controllers\Api\V1\Handlers\TourHandler;
use App\Http\Controllers\Api\V1\Handlers\TouristDestinationHandler;
use App\Http\Controllers\Api\V1\Handlers\TourRequireHandler;
use App\Http\Controllers\Api\V1\Handlers\UserHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiRequest;
use App\Http\Requests\Api\BookingTourRequest;
use App\Http\Requests\Api\CartRequest;
use App\Http\Requests\Api\ContactCustomerRequest;
use App\Http\Requests\Api\CustomerRequest;
use App\Http\Requests\Api\NewsRequest;
use App\Http\Requests\Api\PassengerInformationTourRequest;
use App\Http\Requests\Api\TouristDestinationRequest;
use App\Http\Requests\Api\TourRequest;
use App\Http\Requests\Api\TourRequireRequest;
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

    public function tour(TourRequest $request)
    {
        $this->commandBus->addHandler(TourCommand::class, TourHandler::class);

        $user = $this->commandBus->dispatch(new TourCommand($request));

        return $this->responseSuccess($user->data, $user->message);
    }

    public function TourRequire(TourRequireRequest $request)
    {
        $this->commandBus->addHandler(TourRequireCommand::class, TourRequireHandler::class);

        $user = $this->commandBus->dispatch(new TourRequireCommand($request));

        return $this->responseSuccess($user->data, $user->message);
    }

    public function BookingTour(BookingTourRequest $request)
    {
        $this->commandBus->addHandler(BookingTourCommand::class, BookingTourHandler::class);

        $user = $this->commandBus->dispatch(new BookingTourCommand($request));

        return $this->responseSuccess($user->data, $user->message);
    }

    public function ContactCustomer(ContactCustomerRequest $request)
    {
        $this->commandBus->addHandler(ContactCustomerCommand::class, ContactCustomerHandler::class);

        $user = $this->commandBus->dispatch(new ContactCustomerCommand($request));

        return $this->responseSuccess($user->data, $user->message);
    }

    public function News(NewsRequest $request)
    {
        $this->commandBus->addHandler(NewsCommand::class, NewsHandler::class);

        $command = $this->commandBus->dispatch(new NewsCommand($request));

        return $this->responseSuccess($command->data, $command->message);
    }

    public function Cart(CartRequest $request)
    {
        $this->commandBus->addHandler(CartCommand::class, CartHandler::class);

        $command = $this->commandBus->dispatch(new CartCommand($request));

        return $this->responseSuccess($command->data, $command->message);
    }

    public function PassengerInformationTour(PassengerInformationTourRequest $request)
    {
        $this->commandBus->addHandler(PassengerInformationTourCommand::class, PassengerInformationTourHandler::class);

        $command = $this->commandBus->dispatch(new PassengerInformationTourCommand($request));

        return $this->responseSuccess($command->data, $command->message);
    }

    public function TouristDestination(TouristDestinationRequest $request)
    {
        $this->commandBus->addHandler(TouristDestinationCommand::class, TouristDestinationHandler::class);

        $command = $this->commandBus->dispatch(new TouristDestinationCommand($request));

        return $this->responseSuccess($command->data, $command->message);
    }

    public function Customer(CustomerRequest $request)
    {
        $this->commandBus->addHandler(CustomerCommand::class, CustomerHandler::class);

        $command = $this->commandBus->dispatch(new CustomerCommand($request));

        return $this->responseSuccess($command->data, $command->message);
    }
}
