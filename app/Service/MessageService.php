<?php


namespace App\Service;

use App\Contracts\MessageServiceContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Class MessageService
 * @package App\Service
 */
class MessageService implements MessageServiceContract
{


    /**
     * @var array
     */
    private $messageHeaders;

    /**
     * @var string
     */
    private $message;

    /**
     * @var
     */
    private $currentlyAbsent;
    /**
     * @var
     */
    private $absentNextWeek;
    /**
     * @var
     */
    private $absentUpdate;
    /**
     * @var
     */
    private $absentMonday;

    /**
     * MessageService constructor.
     */
    public function __construct()
    {
        $this->messageHeaders;
        $this->message = '';
        $this->currentlyAbsent;
        $this->absentNextWeek;
        $this->absentUpdate;
        $this->absentMonday;

        // The beginning of each message block
        $this->messageHeaders = [
            'currentlyAbsent' => '*Currently absent*' . "\n",
            'absentNextWeek' => "\n" . '*Absent in the next 7 days*' . "\n",
            'absentUpdate' => '*Updated or new absence*' . "\n",
            'absentMonday' => '*Will be absent on Monday*' . "\n",
        ];
    }

    /**
     *
     */
    public function send(): void
    {
        $this->constructMessage();
        Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
        ])->post(env('WEBHOOK_URL'), [
            'text' => $this->message
        ]);
    }

    /**
     *
     */
    private function constructMessage(): void
    {
        $header = $this->messageHeaders['currentlyAbsent'];
        $this->mapAbsence($this->currentlyAbsent, $header, false);

        $header = $this->messageHeaders['absentNextWeek'];
        $this->mapAbsence($this->absentNextWeek, $header, true);

        $header = $this->messageHeaders['absentUpdate'];
        $this->mapAbsence($this->absentUpdate, $header, true);

        $header = $this->messageHeaders['absentMonday'];
        $this->mapAbsence($this->absentMonday, $header, true);

    }

    /**
     * @param object|null $absence
     * @param string $header
     * @param bool $toggle
     */
    private function mapAbsence(?object $absence, string $header, bool $toggle): void
    {
        if ($this->isSet($absence)) {
            $this->message .= $header;
            foreach ($absence as $absent) {
                $this->messageBody($absent, $toggle);
            }
        }
    }

    /**
     * @param object $absence
     * @param bool $toggle
     */
    private function messageBody(object $absence, bool $toggle): void
    {
        $this->message .= $this->concatenateEmployee($absence->employee, $toggle);
        $this->message .= $this->constructDates($absence->absence_begin, $absence->absence_end, $toggle) . "\n";
        $this->message .= $this->substitutes($absence);
    }

    /**
     * @param object $substitutes
     * @return string
     */
    private function substitutes(object $substitutes): string
    {
        $subs = '';
        $linebreak = '';
        if ($substitutes->substitute_01_id != null) {
            $subs .= 'Please refer to: ';
            $subs .= $this->concatenateEmployee($substitutes->substitute01, false);
            $linebreak = "\n";
        }
        if ($substitutes->substitute_02_id != null) {
            $subs .= ', ' . $this->concatenateEmployee($substitutes->substitute02, false);
        }
        if ($substitutes->substitute_03_id != null) {
            $subs .= ', ' . $this->concatenateEmployee($substitutes->substitute03, false);
        }
        return $subs . $linebreak;
    }

    /**
     * @param object $employee
     * @param bool $isFrom
     * @return string
     */
    private function concatenateEmployee(object $employee, bool $isFrom): string
    {
        $result = $isFrom ? ' from:' : '';
        return $employee->first_name . ' ' . $employee->last_name . $result;

    }

    /**
     * @param $absence_begin
     * @param $absence_end
     * @param bool $isBegin
     * @return string
     */
    private function constructDates($absence_begin, $absence_end, bool $isBegin): string
    {
        $beginDate = $this->formatDates($absence_begin);
        $endDate = $this->formatDates($absence_end);
        return $this->concatenateDateString($beginDate, $endDate, $isBegin);
    }

    /**
     * @param $beginDate
     * @param $endDate
     * @param bool $isBegin
     * @return string
     */
    private function concatenateDateString($beginDate, $endDate, bool $isBegin): string
    {
        $dateString = $isBegin ? " *" . $beginDate . '*' : '';
        return $dateString . " until: *" . $endDate . "* ";
    }

    /**
     * @param $date
     * @return string
     */
    private function formatDates($date)
    {
        return Carbon::parse($date)->format('M d D, Y');
    }

    /**
     * @param object|null $absentType
     * @return bool
     */
    private function isSet(?object $absentType): bool
    {
        return $absentType != null;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param object $currentlyAbsent
     */
    public function setCurrentlyAbsent(object $currentlyAbsent): void
    {
        $this->currentlyAbsent = $currentlyAbsent;
    }

    /**
     * @param object $absentNextWeek
     */
    public function setAbsentNextWeek(object $absentNextWeek): void
    {
        $this->absentNextWeek = $absentNextWeek;
    }

    /**
     * @param object $absentUpdate
     */
    public function setAbsentUpdate(object $absentUpdate): void
    {
        $this->absentUpdate = $absentUpdate;
    }

    /**
     * @param object $absentMonday
     */
    public function setAbsentMonday(object $absentMonday): void
    {
        $this->absentMonday = $absentMonday;
    }

    /**
     * @param object $beginDateToggle
     */
    public function setBeginDateToggle(object $beginDateToggle): void
    {
        $this->beginDateToggle = $beginDateToggle;
    }


}
