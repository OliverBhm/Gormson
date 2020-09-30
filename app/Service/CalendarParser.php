<?php
namespace App\Service;

use App\Contracts\CalendarParserContract;
use Carbon\Carbon;
use ICal\ICal;

/**
 * Class CalendarParser
 * @package App\Service
 */
class CalendarParser implements CalendarParserContract
{
    /**
     * @param string $raw
     * @return array
     */
    public function parseCalendar(string $raw): array
    {
        $parse = $this->parseData($raw);
        return $this->extractEvents($parse);
    }

    /**
     * @param string $rawCalendar
     * @return array
     */
    private function parseData(string $rawCalendar): array
    {
        $ical = new ICal($rawCalendar, [
            'defaultTimeZone' => 'UTC',
        ]);
        return $ical->events();
    }

    /**
     * @param array $parsedCalendar
     * @return array $calendarEvents
     */
    private function extractEvents(array $parsedCalendar): array
    {
        $calendarEvents = [];
        foreach ($parsedCalendar as $event) {
            $summary = $event->summary;
            $absenceType = $this->betweenWords($summary, '-', '(');
            if (in_array($absenceType, $this->acceptedAbsenceTypes(), true)) {
                $calendarEvents[] = [
                    'employee' => $this->betweenWords('.' . $summary, '.', '-'),
                    'substitutes' => $this->substitutes($summary, ':'),
                    "absence_type" => $absenceType,
                    'days' => $this->betweenWords($summary, '(', ' '),
                    "absence_begin" => Carbon::parse($event->dtstart),
                    "absence_end" => Carbon::parse($event->dtend),
                    "created" => $event->created,
                    'updated_at' => $event->last_modified
                ];
            }
        }
        return $calendarEvents;
    }

    /**
     * @param string $haystack
     * @param string $start
     * @param string $end
     * @return string
     */
    function betweenWords(string $haystack, string $start, string $end)
    {
        $substringStart = strpos($haystack, $start);
        $substringStart += strlen($start);
        $size = strpos($haystack, $end, $substringStart) - $substringStart;
        return trim(substr($haystack, $substringStart, $size));
    }

    /**
     * @param string $haystack
     * @param $needle
     * @return string|string[]|null
     */
    private function substitutes(string $haystack, string $needle)
    {
        if (strpos($haystack, 'Vertretung')) {
            $substituteStart = strpos($haystack, $needle);
            $substitutes = substr($haystack, $substituteStart + 2);
            return str_replace(' + ', ', ', $substitutes);
        }
        return null;
    }

    /**
     * @return string[]
     */
    private function acceptedAbsenceTypes()
    {
        return [
            'Urlaub',
            'Krankheit',
            'Berufschule',
            'Freizeitausgleich',
        ];
    }
}

