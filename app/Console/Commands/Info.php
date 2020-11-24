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
     * @var string
     */
    protected $signature = 'absence:Info';

    /**
     * @var string
     */
    protected $description = 'info in the morning';

    /**
     * @param $data
     */
    private function message(array $data)
    {
        $service = app(MessageServiceContract::class);
        $icsData =  app(IcsDataServiceContract::class);

        $service->setCurrentlyAbsent($icsData->currentlyAbsent($data));
        $service->setAbsentNextWeek($icsData->absentInDayRange($data, now(), now()->addWeek()));

        $service->sendDaily();
    }

    /**
     * @param CalendarParserContract $parser
     */
    public function handle(CalendarParserContract $parser)
    {
        $this->message($parser->parseCalendar(env('TIMETAPE_API_URL')));
    }
}
