<?php

namespace App\Console\Commands;


use App\Contracts\MessageServiceContract;
use App\Repository\AbsencesRepositoryContract;
use Illuminate\Console\Command;

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
        $absenceRepository = app(AbsencesRepositoryContract::class);
        $currentlyAbsent = $absenceRepository->currentlyAbsent();
        $nextWeek = $absenceRepository->absentInDayRange(0, 7);

        $message = app(MessageServiceContract::class);
        $message->sendDaily($currentlyAbsent, $nextWeek, null, null);
    }
}
