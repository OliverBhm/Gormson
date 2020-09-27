<?php

namespace App\Contracts;

interface CalendarParserContract
{
    /**
     * @param string $raw
     * @return array
     */
    public function parseCalendar(string $raw): array;
}
