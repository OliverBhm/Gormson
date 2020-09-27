<?php


namespace App\Service;

use App\Contracts\CalendarParserContract;
use App\Contracts\IcsDataServiceContract;

class IcsDataService implements IcsDataServiceContract
{

    private function parser()
    {
        return app(CalendarParserContract::class);
    }

    public function icsData($icsData)
    {
        $parser = $this->parser();
        return $parser->parseCalendar($icsData);
    }

    public function currentlyAbsent(array $events)
    {
        return array_filter($events, function ($event) {
            return $event['absence_begin']
                    ->lte(now()->subDay()) and $event['absence_end']
                    ->gte(now());
        });
    }

    public function absentInDayRange(array $timetape, $startDate, $endDate)
    {
        return array_filter($timetape, function ($event) use ($startDate, $endDate) {
            return $event['absence_begin']->between($startDate, $endDate);
        });
    }
}
