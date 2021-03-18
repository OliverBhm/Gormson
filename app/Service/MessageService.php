<?php

namespace App\Service;

use App\Contracts\MessageServiceContract;
use Illuminate\Support\Facades\Http;

require_once 'vendor/autoload.php';

/**
 * Class MessageService
 * @package App\Service
 */
class MessageService implements MessageServiceContract
{

    /**
     * @var array
     */
    /**
     * @var array
     */
    /**
     * @var array
     */
    /**
     * @var array|string
     */
    private $currentlyAbsent = [], $currentlyInOffice = [], $absentNextWeek = [], $absentMonday = [], $dateFormat = 'D M d, Y';

    /**
     * @param mixed $currentlyAbsent
     */
    public function setCurrentlyAbsent($currentlyAbsent = null): void
    {
        $this->currentlyAbsent = $currentlyAbsent;
    }

    /**
     * @param mixed $currentlyInOffice
     */
    public function setCurrentlyInOffice($currentlyInOffice = null): void
    {
        $this->currentlyInOffice = $currentlyInOffice;
    }

    /**
     * @param mixed $absentNextWeek
     */
    public function setAbsentNextWeek($absentNextWeek = null): void
    {
        $this->absentNextWeek = $absentNextWeek;
    }

    /**
     * @param mixed $absentMonday
     */
    public function setAbsentMonday($absentMonday = null): void
    {
        $this->absentMonday = $absentMonday;
    }

    /**
     *
     */
    public function sendDaily(): bool
    {
        $message = $this->message($this->currentlyAbsent, 'Currently absent', 'dates') . "\n";
        $message .= $this->message($this->absentNextWeek, 'Absent in the next 7 days', 'message') . "\n";
        $message .= $this->message($this->currentlyInOffice, 'Currently in the office', 'dates') . "\n";
        $message .= $this->message($this->absentMonday, 'Will be absent on Monday', 'message');
        $message .= $this->weekendGreeting();
        return $this->send(trim($message));
    }


    /**
     * @return string
     */
    private function weekendGreeting() {
        $hasAbsences = !empty($this->currentlyAbsent) || !empty($this->absentNextWeek);
        $isTodayFriday = $this->isDay(now(), 'Fri');
        return $hasAbsences && $isTodayFriday ?  '*Have a nice weekend!*' : '';
    }

    /**
     * @param $dayToCheck
     * @param $day
     * @return bool
     */
    private function isDay($dayToCheck, $day) {
        $todayString = $dayToCheck->format($this->dateFormat);
        return strpos($todayString, $day) !== false;
    }

    /**
     * @param array|null $absences the parsed events with details
     * @param string $header the text add the start of the message
     * @param string $view the template view
     * @return string the constructed message string
     * @throws \Throwable
     */
    private function message(?array $absences, string $header, string $view): string
    {
        if (!empty($absences)) {
            return strval(view($view)
                ->with([
                    'header' => $header,
                    'dates' => array_map(function ($event) {
                        return [
                            'employee' => $event['employee'],
                            'substitutes' => $event['substitutes'],
                            "absence_type" => $event['absence_type'],
                            "days" => $event['days'],
                            "absence_begin" => $event['absence_begin']->format($this->dateFormat),
                            "absence_end" => $this->saturdayFixed($event['absence_end']),
                        ];
                    }, $absences)
                ])
                ->render()
            );
        }
        return '';
    }

    /**
     * @param $date the one to check
     * @return mixed
     * sometimes the end date is on a saturday, this fixes this
     */
    private function saturdayFixed($date)
    {
        if ($this->isDay($date, 'Sat')) {
            $date->subDay();
        }
        return $date->format($this->dateFormat);
    }

    /**
     * @param string $message to send to the chat
     * @return bool if it was successful
     */
    private function send(string $message): bool
    {
        $isEmpty = strlen($message) < 1;
        if (!$isEmpty) {
            Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
            ])->post(env('WEBHOOK_URL'), [
                'text' => trim($message)
            ]);
            return true;
        }
        return false;
    }
}
