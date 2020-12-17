<?php


namespace App\Service;

use App\Contracts\IcsDataServiceContract;

/**
 * Class IcsDataService
 * @package App\Service
 */
class IcsDataService implements IcsDataServiceContract
{
    /**
     * @param array $events the parsed events with details
     * @return array events whit people currently absent
     */
    public function currentlyAbsent(array $events)
    {
        return array_filter($events, function ($event) {
            return $event['absence_begin']
                    ->lte(now())
                and $event['absence_end']
                    ->gte(now());
        });
    }

    /**
     * @param array $events the parsed events with details
     * @param $startDate the date to start with
     * @param $endDate the last day
     * @return array the events in the given date range
     */
    public function absentInDayRange(array $events, $startDate, $endDate)
    {
        return array_filter($events, function ($event) use ($startDate, $endDate) {
            return $event['absence_begin']
                ->between($startDate, $endDate);
        });
    }
}
