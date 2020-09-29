<?php

namespace App\Contracts;

interface IcsDataServiceContract
{
    public function currentlyAbsent(array $events);

    public function absentInDayRange(array $timetape, $startDate, $endDate);
}
