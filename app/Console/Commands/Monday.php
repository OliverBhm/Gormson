<?php

namespace App\Console\Commands;

use App\Contracts\IcsDataServiceContracts;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;

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


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tomorrow = now()->addDay;
        $threeDays = now()->addDays(3);
        $icsDataService = app(IcsDataServiceContracts::class);
        $data = $icsDataService->icsData();
        $absentMonday = $icsDataService->absentInDayRange($data, $tomorrow, $threeDays);

        $message = app(MessageServiceContract::class);
        $message->sendDaily(null, null, null, $absentMonday);
    }
}
