<?php


namespace App\Service;

use App\Contracts\IcsDataServiceContracts;
use Illuminate\Support\Facades\Http;

/**
 * Class IcsDataService
 * @package App\Service
 */
class IcsDataService implements IcsDataServiceContracts
{
    /**
     * @var mixed
     */
    private $url;

    /**
     * IcsDataService constructor.
     */
    public function __construct()
    {
        $this->url = env('TIMETAPE_API_URL');
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return Http::get($this->url);
    }
}
