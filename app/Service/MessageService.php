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
        ?Collection $currentlyAbsent,
        ?Collection $absentNextWeek,
        ?Collection $absentMonday,
        ?Collection $absenceUpdated
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
    private function message(?Collection $absences, bool $isBeginDisplayed, string $messageHeader): string
    {
        if (!isset($absences) or count($absences) < 1) {
            return '';
        }

        $data = ['header' => $messageHeader, 'dates' => $absences->toArray()];
        if ($isBeginDisplayed) {
            $fromTemplate = view('message')->with($data)->render();
        } else {
            $fromTemplate = view('dates')->with($data)->render();
        }
        return strval($fromTemplate);
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
