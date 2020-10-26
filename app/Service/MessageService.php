<?php

namespace App\Service;

use App\Contracts\MessageServiceContract;
use Illuminate\Support\Facades\Http;

require_once 'vendor/autoload.php';

class MessageService implements MessageServiceContract
{
    private $currentlyAbsent, $absentNextWeek, $absentMonday, $dateFormat = 'D M d, Y';

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

    public function sendDaily(): void
    {

        $message = $this->messageWithoutStartDate($this->currentlyAbsent, 'Currently absent');
        $message .= $this->message($this->absentNextWeek, 'Absent in the next 7 days');
        $message .= $this->message($this->absentMonday, 'Will be absent on Monday');
        $this->send($message);
    }

    private function messageWithoutStartDate(?array $absences, $messageHeader): string
    {
        if ($this->isEmpty($absences)) {
            return '';
        }

        return strval(view('message')
            ->with($this->messageData($absences, $messageHeader))
            ->render()
        );
    }

    private function isEmpty(?array $absences)
    {
        return !isset($absences) or count($absences) < 1;
    }

    private function message(?array $absences, string $messageHeader): string
    {
        if ($this->isEmpty($absences)) {
            return '';
        }

        return strval(view('message')
            ->with($this->messageData($absences, $messageHeader))
            ->render()
        );
    }

    private function messageData(array $absences, $messageHeader)
    {
        return [
            'header' => $messageHeader,
            'dates' => array_map([$this, 'hydrate'], $absences)
        ];
    }

    private function hydrate(array $event)
    {
        $this->dateFormat;
        return [
            'employee' => $event['employee'],
            'substitutes' => $event['substitutes'],
            "absence_type" => $event['absence_type'],
            "days" => $event['days'],
            "absence_begin" => $event['absence_begin']
                ->format($this->dateFormat),
            "absence_end" => $event['absence_end']
                ->format($this->dateFormat),
        ];
    }

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
