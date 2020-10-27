<?php

namespace App\Providers;


use App\Contracts\CalendarParserContract;
use App\Contracts\IcsDataServiceContract;
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
        $this->app->singleton(IcsDataServiceContract::class, IcsDataService::class);
        $this->app->singleton(MessageServiceContract::class, MessageService::class);
        $this->app->singleton(CalendarParserContract::class, CalendarParser::class);
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
