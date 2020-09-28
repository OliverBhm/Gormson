<?php

namespace App\Contracts;

/**
 * Interface MessageServiceContract
 * @package App\Contracts
 */
interface MessageServiceContract
{
    /**
     * @param mixed $currentlyAbsent
     */
    public function setCurrentlyAbsent($currentlyAbsent = null): void;

    /**
     * @param mixed $absentNextWeek
     */
    public function setAbsentNextWeek($absentNextWeek = null): void;

    /**
     * @param mixed $absentMonday
     */
    public function setAbsentMonday($absentMonday = null): void;

    /**
     * set the data for the messages first
     * constructs a message using a blade template
     * it then sends a message to the webook url
     */
    public function sendDaily(): void;
}
