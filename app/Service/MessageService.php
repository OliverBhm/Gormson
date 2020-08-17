<?php


namespace App\Service;

use App\Contracts\MessageServiceContract;
use Carbon\Carbon;
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
     * @param object|null $currentlyAbsent
     * @param object|null $absentNextWeek
     * @param object|null $absentMonday
     * @param object|null $absenceUpdated
     * @throws \Throwable
     */
    public function sendDaily(
        object $currentlyAbsent = null,
        object $absentNextWeek = null,
        object $absentMonday = null,
        object $absenceUpdated = null
    ): void {
        $absences = func_get_args();
        $message = $this->message($absences);
        $this->send($message);
    }

    /**
     * @param string $message
     */
    private function send(string $message) {
        Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
        ])->post(env('WEBHOOK_URL'), [
            'text' => $message
        ]);
    }

    /**
     * @param array $absences
     * @return string
     * @throws \Throwable
     */
    private function message(array $absences): string
    {
        $message = '';
        $isFromDisplayed = false;
        for ($index = 0; $index < count($absences); $index++) {
            if (!isset($absences[$index])) {
                continue;
            }
            if (count($absences[$index]) < 1) {
                break;
            }
            // currently absent is the first index, we display the start date for all other messages
            if ($index > 0) {
                $isFromDisplayed = true;
            }
            $header = $this->getHeader($index);
            $messageBody = $this->body($absences[$index], $isFromDisplayed);
            $message .= $header. $messageBody;
        }
        return $message;
    }

    /**
     * @param object $dates
     * @param bool $isFromDisplayed
     * @return string
     * @throws \Throwable
     */
    private function body(object $dates, bool $isFromDisplayed)
    {
        $text = '';
        foreach ($dates as $date) {
            $this->formatDates($date);
            $absenceTemplate = $this->hydrate($date, $isFromDisplayed);
            $messageFromTemplate = view('message', $absenceTemplate)->render();
            $text .= strval($messageFromTemplate);
        }
        return $text;
    }

    /**
     * @param int $index
     * @return string
     * @throws \Throwable
     */
    private function getHeader(int $index): string
    {
        $text = ['messageHeader' => ''];
        switch ($index) {
            case 0:
                $text = ['messageHeader' => 'Currently Absent'];
                break;
            case 1:
                $text = ['messageHeader' => 'Absent in the next 7 days'];
                break;
            case 2:
                $text = ['messageHeader' => 'Absent on Monday'];
                break;
            case 3:
                $text = ['messageHeader' => 'Absence updated or changed'];
                break;
        }
        return strval(view('header', $text)->render());
    }

    /**
     * @param object $date
     */
    private function formatDates(object &$date): void
    {
        $date->absence_begin = Carbon::Parse($date->absence_begin)->format('d D M Y');
        $date->absence_end = Carbon::Parse($date->absence_end)->format('d D M Y');
    }

    /**
     * @param object $absence
     * @param bool $isFromDisplayed
     * @return array
     */
    private function hydrate(object $absence, bool $isFromDisplayed): array
    {
        return [
            'first_name' => $absence->employee->first_name,
            'last_name' => $absence->employee->last_name,
            'from' => $absence->absence_begin,
            'isFromDisplayed' => $isFromDisplayed,
            'until' => $absence->absence_end,
            'substitute_01_first_name' => $absence->substitute01->first_name ?? null,
            'substitute_01_last_name' => $absence->substitute01->last_name ?? null,
            'substitute_02_first_name' => $absence->substitute02->first_name ?? null,
            'substitute_02_last_name' => $absence->substitute02->last_name ?? null,
            'substitute_03_first_name' => $absence->substitute03->first_name ?? null,
            'substitute_03_last_name' => $absence->substitute03->last_name ?? null,
        ];
    }
}
