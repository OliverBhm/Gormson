<?php


namespace App\Service;

use App\Contracts\CalendarParserContract;
use Carbon\Carbon;
use ICal\ICal;


// ToDo handle hourly leave

/**
 * Class ParseCalendar
 * @package App\Service
 */
class CalendarParser implements CalendarParserContract
{
    private $summaryFilter = [
        'Homeoffice',
        'Feiertag',
        'Arbeitsfeier',
        'Arbeit',
        'beantrag'
    ];

    /**
     * @var array
     */
    private $wrongTokens = [
        '-',
        '+',
        'Tag),',
        'Tage)',
        'Einheit',
    ];
    /**
     * @var array
     */
    private $wrongAbsenceTypes = [
        "Homeoffice",
        "Feiertag",
        'Arbeitsfeier',
        'Deutschen',
        'Arbeit',
        '(halber',
        'Tag)',
        "Einheit",
        'Arztbesuch',
        "Vertretung:",
    ];

    private $absenceTypes = [
        'Urlaub',
        'Krankheit',
        'Berufschule',
    ];

    /**
     * @param string $raw
     * @return array
     */
    public function parseCalendar(string $raw): array
    {
        return $this->extractEvents($this->parseData($raw));
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

    public function getEmployees(array $events): array
    {
        $employeesNames = [];
        foreach ($events as $event) {
            $employeesNames[] = [
                'first_name' => $event['employee']['first_name'],
                'last_name' => $event['employee']['last_name'],
            ];
        }
        return array_unique($employeesNames, SORT_REGULAR);
    }

    /**
     * @param array $parsedCalendar
     * @return array $calendarEvents
     */
    private function extractEvents(array $parsedCalendar): array
    {
        $calendarEvents = [];
        $eventsFiltered = array_filter($parsedCalendar, [$this, 'filterSummary']);
        foreach ($eventsFiltered as $event) {
            $parts = $this->explodeParts($event->summary);
            $calendarEvents[] = [
                'employee' => $this->extractEmployee($parts),
                "absence_type" => $this->extractAbsenceType($parts),
                "absence_id" => $this->extractUid($event->uid),
                "absence_begin" => Carbon::parse($event->dtstart),
                "absence_end" => Carbon::parse($event->dtend)->subDay(),
                "created" => $event->created,
                'updated_at' => $event->last_modified
            ];
        }
        return array_filter($calendarEvents, [$this, "filterEvents"]);
    }

    /**
     * @param $parts
     * @return array
     */
    private function extractEmployee($parts): array
    {
        $results = [];
        if (isset($parts[3])) {
            if (in_array($parts[3], $this->absenceTypes) && isset($parts[4])) {
                $results = [
                    "first_name" => $parts[0],
                    "last_name" => $parts[2],
                    "substitutes" => $this->extractSubstitutes($parts)
                ];
            } else {
                $results = [
                    "first_name" => $parts[0],
                    "last_name" => $parts[1],
                    "substitutes" => $this->extractSubstitutes($parts)
                ];
            }
        }
        return $results;
    }

    /**
     * @param array $parts
     * @return string
     */
    private function extractAbsenceType(array $parts): string
    {
        return $parts[3] == '(0,5' ? 'Half a day' : $parts[2];
    }

    /**
     * @param $uid
     * @return int
     */
    private function extractUid(string $uid): string
    {
        $uidString = strval($uid);
        $vacationId = strstr($uidString, '@', true);
        return $this->splitString($vacationId);
    }

    /**
     * @param $parts
     * @return array
     */
    private function extractSubstitutes(array $parts): array
    {
        $substitutes = [
            0 => [
                "first_name" => '',
                "last_name" => '',
            ],
            1 => [
                "first_name" => '',
                "last_name" => '',
            ],
            2 => [
                "first_name" => '',
                "last_name" => '',
            ]
        ];
        for ($j = 0; $j < count($parts); $j++) {
            if ($parts[$j] == 'Vertretung:') {
                $i = 0;
                for ($k = $j + 1; $k < count($parts) - 1; $k += 2) {
                    $substitutes[$i]['first_name'] = $parts[$k];
                    $substitutes[$i]['last_name'] = $parts[$k + 1] . "\n";
                    $i++;
                }
            }
        }
        return $substitutes;
    }

    /**
     * @param $events
     * @return bool
     */
    private
    function filterEvents(
        $events
    ): bool {
        if (isset($events['absence_type'])) {
            return !in_array($events['absence_type'], $this->wrongAbsenceTypes);
        }
        return false;
    }

    /**
     * @param string $part
     * @return bool
     */
    private
    function filterParts(
        string $part
    ): bool {
        return !in_array($part, $this->wrongTokens);
    }

    /**
     * @param $inputName
     * @return array
     */
    private
    function explodeParts(
        string $inputName
    ): array {
        $parts = explode(' ', $inputName);
        return array_values(array_filter($parts, [$this, 'filterParts']));
    }

    /**
     * @param $event
     * @return bool
     */
    private
    function filterSummary(
        $event
    ) {
        foreach ($this->summaryFilter as $types) {
            if (strpos($event->summary, $types) > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $inputString
     * @return int
     */
    private
    function splitString(
        $inputString
    ) {
        // split string when char occurs
        $parts = preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $inputString);
        return intval($parts[1]);
    }
}


/*  */
