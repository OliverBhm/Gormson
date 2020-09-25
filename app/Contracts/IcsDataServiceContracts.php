<?php

namespace App\Contracts;

/**
 * Interface IcsDataServiceContracts
 * @package App\Contracts
 */
interface IcsDataServiceContracts
{

    public function icsData();
    public function currentlyAbsent(array $timetape);
    public function absentInDayRange(array $timetape, $startDate, $endDate);
}
