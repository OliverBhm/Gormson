<?php

namespace App\Console\Commands;

use App\Contracts\CalendarParserContract;
use App\Contracts\IcsDataServiceContract;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
     * @param $data
     */
    private function message(array $data)
    {
        $service = app(MessageServiceContract::class);
        $icsData =  app(IcsDataServiceContract::class);

        $service->setAbsentMonday($icsData->absentInDayRange($data, now(), now()->addDay()));

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

