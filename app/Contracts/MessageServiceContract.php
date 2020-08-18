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
    public function sendDaily(Collection $currentlyAbsent, Collection $absentNextWeek, Collection $absentMonday, Collection $absenceUpdated): void;
}
