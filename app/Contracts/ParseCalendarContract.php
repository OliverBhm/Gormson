<?php

namespace App\Contracts;

interface ParseCalendarContract
{
    /**
     * @param string $raw
     * @return array
     */
    public function parsedCalendar(string $raw): array;
}
