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


    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $icsDataService = app(IcsDataServiceContract::class);
        $data = $icsDataService->icsData(Http::get(env('TIMETAPE_API_URL')));
        $currentlyAbsent = $icsDataService->currentlyAbsent($data);
        $nextWeek = $icsDataService->absentInDayRange($data, now(), now()->addWeek());
        $message = app(MessageServiceContract::class);
        $message->sendDaily($currentlyAbsent, $nextWeek, null, null);
    }
}
