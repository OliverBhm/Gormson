<?php


namespace App\Service;

use App\Absence;
use App\Contracts\MessageServiceContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Routing\Route;
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
     */
    public function sendDaily(
        Collection $currentlyAbsent = null,
        Collection $absentNextWeek = null,
        Collection $absentMonday = null,
        Collection $absenceUpdated = null
    ): void {
        $message = $this->message($currentlyAbsent, false, 'Currently absent');
        $message .= $this->message($absentNextWeek, true, 'Absent in the next 7 days');
        $message .= $this->message($absentMonday, true, 'Will be absent on Monday');
        $message .= $this->message($absenceUpdated, true, 'Updated or changed');
        $this->send($message);
    }

    private function message(?Collection $absences, bool $isBeginDisplayed, string $messageheader): string
    {
        if (!isset($absences) or count($absences) < 1) {
            return '';
        }
        $data = [
            'header' => $messageheader,
            'isBeginDisplayed' => $isBeginDisplayed,
            'dates' => $absences->toArray()
        ];
        $fromTemplate = view('message')->with($data)->render();
        return strval($fromTemplate);
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
