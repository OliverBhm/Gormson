<?php


namespace App\Service;

use App\Contracts\IcsDataServiceContracts;
use Illuminate\Support\Facades\Http;

class IcsDataService implements IcsDataServiceContracts
{
    private $url;

    public function __construct()
    {
        $this->url = env('TIMETAPE_API_URL');
    }

    public function get(): string
    {
        return Http::get($this->url);
    }
}
