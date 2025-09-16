<?php

namespace App\Providers;

use App\Repositories\Tour\TourInterface;
use App\Repositories\Tour\TourRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(TourInterface::class, TourRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
