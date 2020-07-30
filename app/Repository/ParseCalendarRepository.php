<?php


namespace App\Repository;

use ICal\ICal;


// ToDo handle hourly leave
// Rework naming for methods / varaibles

class ParseCalendarRepository
{

    private $rawCalendar;
    private $parsedCalendar;
    private $filteredCalendar;
    private $wrongLeaveTypes;

    public function __construct()
    {
        $this->parsedCalendar = [];
        $this->filteredCalendar = [];
        $this->wrongLeaveTypes = [
            "Homeoffice",
            "Feiertag",
            "Einheit",
            "Tag)",
            "-",
        ];
    }

    /**
     * @return mixed
     */
    public function getRawCalendar()
    {
        return $this->rawCalendar;
    }

    /**
     * @param mixed $rawCalendar
     */
    public function setRawCalendar($rawCalendar): void
    {
        $this->rawCalendar = $rawCalendar;
    }

    /**
     * @return array
     */
    public function getParsedCalendar(): array
    {
        return $this->parsedCalendar;
    }

    /**
     * @param array $parsedCalendar
     */
    public function setParsedCalendar(array $parsedCalendar): void
    {
        $this->parsedCalendar = $parsedCalendar;
    }

    public function parsedCalendar()
    {
        $this->parseData();
        $this->extractEvents();
        return collect($this->filteredCalendar);
    }

    public function parseData()
    {
        $ical = new ICal($this->rawCalendar, array(
            'defaultSpan' => 2,     // Default value
            'defaultTimeZone' => 'UTC',
            'defaultWeekStart' => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter' => null,  // Default value
            'filterDaysBefore' => null,  // Default value
            'skipRecurrence' => false, // Default value
        ));
        $this->parsedCalendar = $ical->events();;
    }

    private function extractEvents()
    {
        $calendarEvents = [];
        foreach ($this->parsedCalendar as $event) {
            $summary = $event->summary;
            $calendarEvents[] = ["employee" => $this->extractEventDetails($summary),
                "vacationId" => $this->extractUid($event->uid),
                "leaveStart" => $event->dtstart,
                "leaveEnd" => $event->dtend,
                "created" => $event->created];
        }

        $this->filteredCalendar = array_filter($calendarEvents, array($this, "filterEvents"));

    }

    private function extractEventDetails($inputName)
    {
        $results = [];
        $parts = explode(' ', $inputName);

        if (array_key_exists(3, $parts)) {
            $results = ["firstname" => $parts[0],
                "lastname" => $parts[1],
                "leavetype" => $parts[3],
                "substitutes" => $this->extractSubstitutes($parts)];
        }
        return $results;
    }

    private function extractUid($uidInput)
    {
        $uidString = strval($uidInput);
        $vacationId = strstr($uidString, '@', true);
        return $this->splitString($vacationId);
    }

    private function extractSubstitutes($parts)
    {
        $substitutes = [];
        if (array_key_exists(6, $parts)) {
            if ($parts[6] == 'Vertretung:') {
                $substitutes["firstname01"] = $parts[7];
                $substitutes["lastname01"] = $parts[8];
            }
        }
        if (array_key_exists(9, $parts)) {
            if ($parts[9] == '+') {
                $results["firstname02"] = $parts[10];
                $results["lastname02"] = $parts[11];
            }
        }

        if (array_key_exists(12, $parts)) {
            if ($parts[12] == '+') {
                $results["firstname03"] = $parts[13];
                $results["lastname03"] = $parts[14];
            }
        }
        return $substitutes;
    }

    private function filterEvents($events)
    {
        if (isset($events["employee"]["leavetype"])) {
            return !in_array($events["employee"]["leavetype"], $this->wrongLeaveTypes);
        }
        return false;
    }

    private function splitString($inputString)
    {
        // seperate the id from the 'urlaub'
        $parts = preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $inputString);
        return intval($parts[1]);
    }
}
