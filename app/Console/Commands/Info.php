<?php

namespace App\Console\Commands;


use App\Contracts\IcsDataServiceContract;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Info extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:Info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'info in the morning';

    private function message($currentlyAbsent, $nextWeek)
    {
        $service = app(MessageServiceContract::class);
        $service->setCurrentlyAbsent($currentlyAbsent);
        $service->setAbsentNextWeek($nextWeek);
        $service->sendDaily();
    }

    private function parsing(string $url)
    {
        return app(IcsDataServiceContract::class)
            ->icsData(Http::get($url));
    }

    private function currentlyAbsent($data)
    {
        return app(IcsDataServiceContract::class)
            ->currentlyAbsent($data);
    }

    private function absentNextWeek($data)
    {
        $nextWeek = now()->addWeek();
        return app(IcsDataServiceContract::class)
            ->absentInDayRange($data, now(), $nextWeek);
    }

    public function handle()
    {
        $data = $this->parsing(env('TIMETAPE_API_URL'));
        $currentlyAbsent = $this->currentlyAbsent($data);
        $absentNextWeek = $this->absentNextWeek($data);
        $this->message($currentlyAbsent, $absentNextWeek);
    }
}
