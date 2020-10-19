<?php

namespace App\Console\Commands;


use App\Contracts\CalendarParserContract;
use App\Contracts\IcsDataServiceContract;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;

/**
 * Class Info
 * @package App\Console\Commands
 */
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
     * @param $currentlyAbsent
     * @param $nextWeek
     */
    private function message($currentlyAbsent, $nextWeek)
    {
        $service = app(MessageServiceContract::class);
        $service->setCurrentlyAbsent($currentlyAbsent);
        $service->setAbsentNextWeek($nextWeek);
        $service->sendDaily();
    }

    /**
     * @param $data
     */
    private function currentlyAbsent($data)
    {
        app(IcsDataServiceContract::class)
            ->currentlyAbsent($data);
    }

    /**
     * @param $data
     * @param $nextWeek
     * @return mixed
     */
    private function absentNextWeek($data, $nextWeek)
    {
        return app(IcsDataServiceContract::class)
            ->absentInDayRange($data, now(), $nextWeek);
    }

    /**
     * @param CalendarParserContract $parser
     */
    public function handle(CalendarParserContract $parser)
    {
        $data = $parser->parseCalendar(env('TIMETAPE_API_URL'));
        $currentlyAbsent = $this->currentlyAbsent($data);
        $absentNextWeek = $this->absentNextWeek($data, now()->addWeek());
        $this->message($currentlyAbsent, $absentNextWeek);
    }
}
