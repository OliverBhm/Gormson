<?php


namespace App\Service;

use App\Contracts\IcsDataServiceContract;

class IcsDataService implements IcsDataServiceContract
{
    public function currentlyAbsent(array $events)
    {
        return array_filter($events, function ($event) {
            return $event['absence_begin']
                    ->lte(now())
                and $event['absence_end']
                    ->gte(now());
        });
    }

    public function absentInDayRange(array $events, $startDate, $endDate)
    {
        return array_filter($events, function ($event) use ($startDate, $endDate) {
            return $event['absence_begin']
                ->between($startDate, $endDate);
        });
    }
}
