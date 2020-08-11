<?php

namespace App\Providers;


use App\Contracts\CalendarParserContract;
use App\Repository\AbsenceRepository;
use App\Repository\AbsenceRepositoryContract;
use App\Service\MessageService;
use App\Contracts\MessageServiceContract;
use App\Service\CalenderParser;
use App\Service\IcsDataService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('IcsData', IcsDataService::class);
        $this->app->bind(AbsenceRepositoryContract::class, AbsenceRepository::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);
        $this->app->bind(CalendarParserContract::class, CalenderParser::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         Schema::defaultStringLength(191);
    }
}
