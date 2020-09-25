<?php


namespace App\Service;

use App\Contracts\CalendarParserContract;
use App\Contracts\IcsDataServiceContracts;
use Illuminate\Support\Facades\Http;

/**
 * Class IcsDataService
 * @package App\Service
 */
class IcsDataService implements IcsDataServiceContracts
{
    public function icsData() {
        $url = env('TIMETAPE_API_URL');
        $timetape = Http::get($url);
        return (function () use ($timetape){
            $calender = app(CalendarParserContract::class);
            return $calender->parseCalendar($timetape);
        })();
    }

    public function currentlyAbsent(array $timetape) {
        return array_filter($timetape, function($event) {
            return $event['absence_begin']->lte(now()) and $event['absence_end']->gte(now());
        });
    }

    public function absentInDayRange(array $timetape, $startDate, $endDate) {
        return array_filter($timetape, function($event) use($startDate, $endDate) {
            $nextWeek = today()->addWeek();
            return $event['absence_begin']->lte($startDate) and $event['absence_begin']->gte($endDate);
        });
    }
}
