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
     * used to filter out unwanted absences
     */
    private $acceptedAbsenceTypes = [
        'Urlaub',
        'Krankheit',
        'Berufsschule',
        'Freizeitausgleich',
        'Messebesuch',
        'Unbezahlter Urlaub',
        'Schulung/Fortbildung',
        'Unbezahlter Urlaub',
        'Elternzeit',
        'Dienstreise',
    ];

    /**
     * @param string $raw the raw string
     * @return array the parsed Calendar
     */
    public function parseCalendar(string $raw): array
    {
        return $this->extractEvents(
            $this->parseData($raw)
        );
    }

    /**
     * @param string $rawCalendar the .ics file
     * @return array with filtered events
     */
    function parseData(string $rawCalendar): array
    {
        $ical = new ICal($rawCalendar, [
            'defaultTimeZone' => 'UTC',
        ]);
        return $ical->events();
    }

    /**
     * @param array $parsedCalendar from the parser
     * @return array events with details
     */
     function extractEvents(array $parsedCalendar): array
    {
        $calendarEvents = [];
        foreach ($parsedCalendar as $event) {
            $summary = $event->summary;
            $absenceType = $this->betweenStrings($summary, '-', '(');
            $isAcceptedAbsenceType = in_array($absenceType, $this->acceptedAbsenceTypes, true);
            if ($isAcceptedAbsenceType) {
                $calendarEvents[] = [
                    // we add a char before the string to be able to search it
                    'employee' => $this->betweenStrings('.' . $summary, '.', '-'),
                    'substitutes' => $this->substitutes($summary, ':'),
                    "absence_type" => $absenceType,
                    'days' => $this->betweenStrings($summary, '(', ' '),
                    "absence_begin" => Carbon::parse($event->dtstart),
                    "absence_end" => Carbon::parse($event->dtend),
                ];
            }
        }
        return $calendarEvents;
    }

    /**
     * @param string $haystack the string to search
     * @param string $start the first char to start with
     * @param string $end the char to end with
     * @return string the string between the start and end strings
     */
    function betweenStrings(string $haystack, string $start, string $end)
    {
        $substringStart = strpos($haystack, $start) + strlen($start);
        $size = strpos($haystack, $end, $substringStart) - $substringStart;
        return trim(substr($haystack, $substringStart, $size));
    }

    /**
     * @param string $haystack the string to search
     * @param string $needle the string to search for
     * @return string|string[]|null the substitutes, comma separated
     */
    function substitutes(string $haystack, string $needle)
    {
        $containsSubstring = strpos($haystack, 'Vertretung');
        if ($containsSubstring) {
            $substituteStart = strpos($haystack, $needle);
            $substitutes = substr($haystack, $substituteStart + 2);
            return str_replace(' + ', ', ', $substitutes);
        }
        return null;
    }
}
