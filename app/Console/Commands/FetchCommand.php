<?php

namespace App\Console\Commands;
use App\Contracts\CalendarParserContract;
use App\Facade\IcsData;
use App\Repository\AbsenceRepositoryContract;
use Illuminate\Console\Command;

class FetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:FetchCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting and parsing timetape data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rawData = IcsData::get();
        $calender = app(CalendarParserContract::class);
        $events = $calender->parseCalendar($rawData);

        $absenceRepository = app(AbsenceRepositoryContract::class);
        $absenceRepository->deleteObsolete($events);
        foreach ($events as $event) {
            $absenceRepository->create($event);
        }

    }
}
