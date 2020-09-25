<?php

namespace App\Contracts;


use App\Absence;
use Illuminate\Database\Eloquent\Collection;

interface MessageServiceContract
{
    /**
     * @param Absence $currentlyAbsent
     * @param Absence $absentNextWeek
     * @param Absence $absentMonday
     * @param Absence $absenceUpdated
     */
    public function sendDaily(?array $currentlyAbsent, ?array $absentNextWeek, ?array $absentMonday, ?array $absenceUpdated): void;
}
