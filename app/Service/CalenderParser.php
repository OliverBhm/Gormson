<?php


namespace App\Service;

use App\Contracts\CalendarParserContract;
use ICal\ICal;


// ToDo handle hourly leave

/**
 * Class ParseCalendar
 * @package App\Service
 */
class CalenderParser implements CalendarParserContract
{
    private $summaryFilter;
    /**
     * @var array
     */
    private $results;
    /**
     * @var array
     */
    private $wrongTokens;
    /**
     * @var array
     */
    private $wrongAbsenceTypes;
    /**
     * @var array
     */
    private $calendarEvents;

    /**
     * ParseCalendar constructor.
     */
    public function __construct()
    {
        $this->wrongAbsenceTypes = [
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

        $this->summaryFilter = [
            'Homeoffice',
            'Feiertag',
            'Arbeitsfeier',
            'Arbeit',
        ];

        $this->wrongTokens = [
            '-',
            '+',
            'Tag),',
            'Tage)',
            'Einheit',
        ];

        $this->calendarEvents = [
            "absence_id" => "absence_id",
            "absence_begin" => "absence_begin",
            "absence_end" => "absence_end",
            "created" => "created",
        ];

        $this->results = [
            "first_name" => "first_name",
            "last_name" => "last_name",
            "absence_type" => "Homeoffice",
            "substitutes" => [
                0 => [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                ],
                1 => [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                ],
                2 => [
                    'first_name' => 'first_name',
                    'last_name' => 'last_name',
                ],
            ]
        ];
    }


    /**
     * @param string $raw
     * @return array
     */
    public function parseCalendar(string $raw):array
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

    /**
     * @param array $parsedCalendar
     * @return array
     */
    private function extractEvents(array $parsedCalendar): array
    {
        $eventsFiltered = array_filter($parsedCalendar, array($this, 'filterSummary'));
        foreach ($eventsFiltered as $event) {
            $summary = $event->summary;
            $this->calendarEvents[] = [
                'employee' => $this->extractEventDetails($summary),
                "absence_id" => $this->extractUid($event->uid),
                "absence_begin" => $event->dtstart,
                "absence_end" => $event->dtend,
                "created" => $event->created
            ];
        }
        return array_filter($this->calendarEvents, array($this, "filterEvents"));
    }

    /**
     * @param $rawDetails
     * @return array
     */
    private function extractEventDetails($rawDetails): array
    {
        $parts = $this->getParts($rawDetails);
        if (array_key_exists(3, $parts)) {
            $this->results = [
                "first_name" => $parts[0],
                "last_name" => $parts[1],
                "absence_type" => $this->extractLeaveType($parts),
                "substitutes" => $this->extractSubstitutes($parts)
            ];
        }
        return $this->results;
    }

    /**
     * @param array $leaveTypeInput
     * @return string
     */
    private function extractLeaveType(array $leaveTypeInput): string
    {
        return $leaveTypeInput[3] == '(0,5' ? 'Half a day' : $leaveTypeInput[2];
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
    private function filterEvents($events): bool
    {
        if (isset($events['employee']['absence_type'])) {
            return !in_array($events['employee']['absence_type'], $this->wrongAbsenceTypes);
        }
        return false;
    }

    /**
     * @param string $part
     * @return bool
     */
    private function filterParts(string $part): bool
    {
        return !in_array($part, $this->wrongTokens);
    }

    /**
     * @param $inputName
     * @return array
     */
    private function getParts(string $inputName): array
    {
        $parts = explode(' ', $inputName);
        return array_values(array_filter($parts, array($this, 'filterParts')));
    }

    /**
     * @param $event
     * @return bool
     */
    private function filterSummary($event)
    {
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
    private function splitString($inputString)
    {
        // seperate the id from 'urlaub'
        $parts = preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $inputString);
        return intval($parts[1]);
    }
}


/*  */
