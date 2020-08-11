<?php

namespace App\Console\Commands;


use App\Contracts\MessageServiceContract;
use App\Repository\AbsenceRepositoryContract;
use Illuminate\Console\Command;

class info extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:info';

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
        $absenceRepository = app(AbsenceRepositoryContract::class);
        $currentlyAbsent = $absenceRepository->currentlyAbsent();
        $nextWeek = $absenceRepository->absentInDayRange(0, 7);


        $message = app(MessageServiceContract::class);
        $message->setCurrentlyAbsent($currentlyAbsent);
        $message->setAbsentNextWeek($nextWeek);
        $message->send();
    }
}
