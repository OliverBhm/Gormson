<?php

namespace App\Console\Commands;


use App\Contracts\IcsDataServiceContract;
use App\Contracts\MessageServiceContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Info extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absence:Info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'info in the morning';

    private function message($currentlyAbsent, $nextWeek)
    {
        $message = app(MessageServiceContract::class);
        $message->setCurrentlyAbsent($currentlyAbsent);
        $message->setAbsentNextWeek($nextWeek);
        $message->sendDaily();
    }


    private function icsData($contract, $url)
    {
        $data = $contract->icsData(Http::get($url));
        $nextWeek = now()->addWeek();

        $result['currentlyAbsent'] = $contract->currentlyAbsent($data);
        $result['nextWeek'] = $contract->absentInDayRange($data, now(), $nextWeek);
        return $result;
    }

    public function handle()
    {
        $data = $this->icsData(
            app(IcsDataServiceContract::class),
            env('TIMETAPE_API_URL')
        );
        $this->message($data['currentlyAbsent'], $data['nextWeek']);
    }
}
