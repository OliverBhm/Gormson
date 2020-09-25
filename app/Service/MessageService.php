<?php


namespace App\Service;

use App\Contracts\MessageServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

require_once 'vendor/autoload.php';

/**
 * Class MessageService
 * @package App\Service
 */
class MessageService implements MessageServiceContract
{
    /**
     * @param Collection|null $currentlyAbsent
     * @param Collection|null $absentNextWeek
     * @param Collection|null $absentMonday
     * @param Collection|null $absenceUpdated
     * @throws \Throwable
     */
    public function sendDaily(
        ?array $currentlyAbsent,
        ?array $absentNextWeek,
        ?array $absentMonday,
        ?array $absenceUpdated
    ): void {
        $message = $this->message($currentlyAbsent, false, 'Currently absent');
        $message .= $this->message($absentNextWeek, true, 'Absent in the next 7 days');
        $message .= $this->message($absentMonday, true, 'Will be absent on Monday');
        $message .= $this->message($absenceUpdated, true, 'Updated or changed');
        $this->send($message);
    }

    /**
     * @param Collection|null $absences
     * @param bool $isBeginDisplayed
     * @param string $messageHeader
     * @return string
     * @throws \Throwable
     */
    private function message(?array $absences, bool $isBeginDisplayed, string $messageHeader): string
    {
        if (!isset($absences) or count($absences) < 1) {
            return '';
        }

        $dates = array_map([$this, 'formatDates'], $absences);
        $data = ['header' => $messageHeader, 'dates' => $dates];
        if ($isBeginDisplayed) {
            $fromTemplate = view('message')->with($data)->render();
        } else {
            $fromTemplate = view('dates')->with($data)->render();
        }
        return strval($fromTemplate);
    }

    private function formatDates($event)
    {
        return [
            'employee' => $event['employee'],
            "absence_type" => $event['absence_type'],
            "absence_id" => $event['absence_id'],
            "absence_begin" => $event['absence_begin']->format('D M d, Y'),
            "absence_end" => $event['absence_end']->format('D M d, Y'),
            "created" => $event['created'],
            'updated_at' => $event['updated_at']
        ];
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
