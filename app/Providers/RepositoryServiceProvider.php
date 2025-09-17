<?php

namespace App\Providers;

use App\Repositories\BookingTour\BookingTourInterface;
use App\Repositories\BookingTour\BookingTourRepository;
use App\Repositories\Tour\TourInterface;
use App\Repositories\Tour\TourRepository;
use App\Repositories\TourRequire\TourRequireInterface;
use App\Repositories\TourRequire\TourRequireRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(TourInterface::class, TourRepository::class);
        $this->app->bind(TourRequireInterface::class, TourRequireRepository::class);
        $this->app->bind(BookingTourInterface::class, BookingTourRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
