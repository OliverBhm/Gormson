<?php

namespace App\Console\Commands;

use App\Contracts\CalendarParserContract;
use App\Facade\IcsData;
use App\Repository\AbsencesRepositoryContract;
use App\Repository\EmployeesRepositoryContract;
use Illuminate\Console\Command;

/**
 * Class FetchCommand
 * @package App\Console\Commands
 */
class Fetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:Fetch';

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

        $employeeRepository = app(EmployeesRepositoryContract::class);
        foreach ($events as $event) {
            $employeeRepository->create($event['employee']);
        }

        $absenceRepository = app(AbsencesRepositoryContract::class);
        $absenceRepository->deleteObsolete($events);
        foreach ($events as $event) {
            $absenceRepository->create($event);
        }

    }
}
