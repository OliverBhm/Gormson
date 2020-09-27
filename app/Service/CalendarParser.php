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
     * @var string[]
     */
    private $acceptedAbsenceTypes = [
        'Urlaub',
        'Krankheit',
        'Berufschule',
        'Freizeitausgleich',
    ];

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
            'defaultSpan' => 2,     // Default value
            'defaultTimeZone' => 'UTC',
            'defaultWeekStart' => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter' => null,  // Default value
            'filterDaysBefore' => null,  // Default value
            'skipRecurrence' => false, // Default value
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
            if (in_array($absenceType, $this->acceptedAbsenceTypes, true)) {
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
     * @param $string
     * @param $start
     * @param $end
     * @return string
     */
    function betweenWords($string, $start, $end)
    {
        $substringStart = strpos($string, $start);
        $substringStart += strlen($start);
        $size = strpos($string, $end, $substringStart) - $substringStart;
        return trim(substr($string, $substringStart, $size));
    }

    /**
     * @param string $haystack
     * @param $needle
     * @return string|string[]|null
     */
    private function substitutes(string $haystack, $needle)
    {
        if (strpos($haystack, 'Vertretung')) {
            $substituteStart = strpos($haystack, $needle);
            $substitutes = substr($haystack, $substituteStart + 2);
            return str_replace(' + ', ', ', $substitutes);
        }
        return null;
    }
}

