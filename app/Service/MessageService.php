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
    public function sendDaily(
        object $currentlyAbsent = null,
        object $absentNextWeek = null,
        object $absentMonday = null,
        object $absenceUpdated = null
    ): void {
        $absences = func_get_args();
        $message = $this->message($absences);
        echo $message;
    }

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
            if ($index > 0) {
                $isFromDisplayed = true;
            }
            $header = $this->getHeader($index);
            $messageBody = $this->body($absences[$index], $isFromDisplayed);
            $message .= $header . $messageBody;
        }
        return $message;
    }

    private function body(object $dates, $isFromDisplayed)
    {
        $message = '';
        foreach ($dates as $date) {
            $this->formatDates($date);
            $absenceTemplate = $this->hydrate($date, $isFromDisplayed);
            $messageFromTemplate = view('message', $absenceTemplate)->render();
            $message .= strval($messageFromTemplate);
        }
        return $message;
    }

    private function getHeader(int $index): string
    {
        switch ($index) {
            case 0:
                return 'Currently Absent' . "\n";
            case 1:
                return 'Absent in the next 7 days' . "\n";
            case 2:
                return 'Absent on Monday' . "\n";
            case 3:
                return 'Absence updated or changed' . "\n";
        }
    }

    private function formatDates(object &$date): void
    {
        $date->absence_begin = Carbon::Parse($date->absence_begin)->format('d D M Y');
        $date->absence_end = Carbon::Parse($date->absence_end)->format('d D M Y');
    }

    private function hydrate(object $absence, $isFromDisplayed): array
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
