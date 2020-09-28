<?php

namespace App\Console\Commands;

use App\Contracts\IcsDataServiceContract;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Monday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:Monday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Info who is not there on the next Monday';


    private function message($absentMonday)
    {
        $service = app(MessageServiceContract::class);
        $service->setAbsentMonday($absentMonday);
        $service->sendDaily();
    }

    private function parsing(string $url)
    {
        return app(IcsDataServiceContract::class)
            ->icsData(Http::get($url));
    }

    private function absentMonday($data)
    {
        $tomorrow = now()->addDay();
        $monday = now()->addDays(3);
        return app(IcsDataServiceContract::class)
            ->absentInDayRange($data, $tomorrow, $monday);
    }

    public function handle()
    {
        $data = $this->parsing(env('TIMETAPE_API_URL'));
        $absentMonday = $this->absentMonday($data);
        $this->message($absentMonday);
    }
}
