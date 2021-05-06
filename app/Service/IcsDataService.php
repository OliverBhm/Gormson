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
     * @var string[]
     * used to filter out unwanted absences
     */
    private $acceptedAbsenceTypes = [
        'Urlaub',
        'Krankheit',
        'Berufsschule',
        'Freizeitausgleich',
        'Messebesuch',
        'Unbezahlter Urlaub',
        'Schulung/Fortbildung',
        'Unbezahlter Urlaub',
        'Elternzeit',
        'Dienstreise',
    ];

    /**
     * @param array $events the parsed events with details
     * @return array events whit people currently absent
     */
    public function currentlyAbsent(array $events): array
    {
        return array_filter($events, function ($event) {
            $isAcceptedAbsenceType = in_array($event['absence_type'], $this->acceptedAbsenceTypes, true);
            return $event['absence_begin']
                    ->lte(now())
                && $event['absence_end']
                    ->gte(now())
                && $isAcceptedAbsenceType;
        });
    }

    /**
     * @param array $events the parsed events with details
     * @param $startDate the date to start with
     * @param $endDate the last day
     * @return array the events in the given date range
     */
    public function absentInDayRange(array $events, $startDate, $endDate): array
    {
        return array_filter($events, function ($event) use ($startDate, $endDate) {
            $isAcceptedAbsenceType = in_array($event['absence_type'], $this->acceptedAbsenceTypes, true);
            return $event['absence_begin']
                ->between($startDate, $endDate)
                && $isAcceptedAbsenceType;
        });
    }
}
