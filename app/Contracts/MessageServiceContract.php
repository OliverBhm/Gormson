<?php

namespace App\Contracts;


interface MessageServiceContract
{
    /**
     * @param object $currentlyAbsent
     * @param object $absentNextWeek
     * @param object $absentMonday
     * @param object $absenceUpdated
     */
    public function sendDaily(object $currentlyAbsent,object $absentNextWeek,object $absentMonday,object $absenceUpdated): void;
}
