<?php


namespace App\Service;

use App\Contracts\MessageServiceContract;
use Illuminate\Support\Facades\Http;

require_once 'vendor/autoload.php';


class MessageService implements MessageServiceContract
{
    private $currentlyAbsent;
    private $absentNextWeek;
    private $absentMonday;

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

        $message = $this->message($this->currentlyAbsent, false, 'Currently absent');
        $message .= $this->message($this->absentNextWeek, true, 'Absent in the next 7 days');
        $message .= $this->message($this->absentMonday, true, 'Will be absent on Monday');
        $this->send($message);
    }

    private function message(?array $absences, bool $isBeginDisplayed, string $messageHeader): string
    {
        if (!isset($absences) or count($absences) < 1) {
            return '';
        }

        $dates = array_map([$this, 'hydrate'], $absences);
        $data = ['header' => $messageHeader, 'dates' => $dates];
        if ($isBeginDisplayed) {
            return strval(view('message')->with($data)->render());
        } else {
            return strval(view('dates')->with($data)->render());
        }
    }

    private function hydrate(array $event)
    {
        $dateFormat = 'D M d, Y';
        return [
            'employee' => $event['employee'],
            'substitutes' => $event['substitutes'],
            "absence_type" => $event['absence_type'],
            "days" => $event['days'],
            "absence_begin" => $event['absence_begin']->format($dateFormat),
            "absence_end" => $event['absence_end']->format($dateFormat),
            "created" => $event['created'],
            'updated_at' => $event['updated_at']
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
