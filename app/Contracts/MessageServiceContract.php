<?php

namespace App\Contracts;

interface MessageServiceContract
{
    /**
     * @param mixed $currentlyAbsent
     */
    public function setCurrentlyAbsent($currentlyAbsent): void;

    /**
     * @param mixed $absentNextWeek
     */
    public function setAbsentNextWeek($absentNextWeek): void;

    /**
     * @param mixed $absentMonday
     */
    public function setAbsentMonday($absentMonday): void;

    /**
     * @param mixed $absenceUpdated
     */
    public function setAbsenceUpdated($absenceUpdated): void;

    public function sendDaily(): void;
}
