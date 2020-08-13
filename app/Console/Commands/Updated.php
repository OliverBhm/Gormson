<?php

namespace App\Console\Commands;

use App\Contracts\MessageServiceContract;
use App\Repository\AbsencesRepositoryContract;
use Illuminate\Console\Command;

class Updated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:Updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending if updates occured';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $absenceRepository = app(AbsencesRepositoryContract::class);
        $updates = $absenceRepository->absenceUpdated();

        $message = app(MessageServiceContract::class);
        $message->setAbsentUpdate($updates);
        $message->send();

    }
}
