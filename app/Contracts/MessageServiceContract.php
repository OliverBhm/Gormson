<?php

namespace App\Contracts;

interface MessageServiceContract
{
    public function send(): void;

    /**
     * @param mixed $message
     */
    public function setMessage(string $message): void;

    /**
     * @param mixed $currentlyAbsent
     */
    public function setCurrentlyAbsent(object $currentlyAbsent): void;

    /**
     * @param mixed $absentNextWeek
     */
    public function setAbsentNextWeek(object $absentNextWeek): void;

    /**
     * @param mixed $absentUpdate
     */
    public function setAbsentUpdate(object $absentUpdate): void;

    /**
     * @param mixed $absentMonday
     */
    public function setAbsentMonday(object $absentMonday): void;

    /**
     * @param mixed $beginDateToggle
     */
    public function setBeginDateToggle(object $beginDateToggle): void;
}
