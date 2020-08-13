<?php

namespace App\Providers;


use App\Contracts\CalendarParserContract;
use App\Employee;
use App\Repository\AbsencesRepository;
use App\Repository\AbsencesRepositoryContract;
use App\Repository\EmployeesRepository;
use App\Repository\EmployeesRepositoryContract;
use App\Service\MessageService;
use App\Contracts\MessageServiceContract;
use App\Service\CalendarParser;
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
        $this->app->bind(AbsencesRepositoryContract::class, AbsencesRepository::class);
        $this->app->bind(EmployeesRepositoryContract::class, EmployeesRepository::class);
        $this->app->bind(MessageServiceContract::class, MessageService::class);
        $this->app->bind(CalendarParserContract::class, CalendarParser::class);
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
