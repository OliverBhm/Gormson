<?php

namespace App\Console\Commands;

use App\Contracts\MessageServiceContract;
use App\Repository\AbsenceRepositoryContract;
use Illuminate\Console\Command;

class monday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:monday';

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
        $absenceRepository = app(AbsenceRepositoryContract::class);
        $absentMonday = $absenceRepository->absentInDayRange(1, 3);

        $message = app(MessageServiceContract::class);
        $message->setAbsentMonday($absentMonday);
        $message->send();
    }
}
