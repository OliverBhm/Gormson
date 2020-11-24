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
    private $currentlyAbsent = [], $absentNextWeek = [], $absentMonday = [], $dateFormat = 'D M d, Y';

    /**
     * @param mixed $currentlyAbsent
     */
    public function setCurrentlyAbsent($currentlyAbsent = null): void
    {
        $this->currentlyAbsent = $currentlyAbsent;
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
        $message .= $this->message($this->absentMonday, 'Will be absent on Monday', 'message');
        return $this->send($message);
    }

    /**
     * @param array|null $absences
     * @param string $header
     * @param string $view
     * @return string
     * @throws \Throwable
     */
    private function message(?array $absences, string $header, string $view): string
    {
        $isEmpty = count($absences) > 0;
        if ($isEmpty) {
            return strval(view($view)
                ->with([
                    'header' => $header,
                    'dates' => array_map(function ($event) use ($header) {
                        return [
                            'employee' => $event['employee'],
                            'substitutes' => $event['substitutes'],
                            "absence_type" => $event['absence_type'],
                            "days" => $event['days'],
                            "absence_begin" => $event['absence_begin']->format($this->dateFormat),
                            "absence_end" => $this->isSaturday($event['absence_end']),
                        ];
                    }, $absences)
                ])
                ->render()
            );
        }
        return '';
    }

    /**
     * @param $date
     * @return mixed
     * sometimes the end date is on a saturday, this fixes this
     */
    private function isSaturday($date)
    {
        $dateAsString = $date->format($this->dateFormat);
        $isSaturday = strpos($dateAsString, 'Sat') !== false;
        if ($isSaturday) {
            $date->subDay();
        }
        return $date->format($this->dateFormat);
    }

    /**
     * @param string $message
     * @return bool
     */
    private function send(string $message): bool
    {
        if (strlen($message) > 0) {
            Http::withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
            ])->post(env('WEBHOOK_URL'), [
                'text' => $message
            ]);
            return true;
        }
        return false;
    }
}
