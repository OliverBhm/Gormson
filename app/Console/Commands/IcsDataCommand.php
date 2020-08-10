<?php

namespace App\Console\Commands;
use App\Contracts\ParseCalendarContract;
use App\Facade\IcsData;
use App\Repository\AbsenceRepositoryInterface;
use Illuminate\Console\Command;

class IcsDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:IcsDataCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting and parsing timetape data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rawData = IcsData::get();
        $calender = app(ParseCalendarContract::class);
        $events = $calender->parsedCalendar($rawData);

        $absenceRepository = app(AbsenceRepositoryInterface::class);
        $absenceRepository->deleteObsolete($events);
        foreach ($events as $event) {
            $absenceRepository->create($event);
        }

    }
}
