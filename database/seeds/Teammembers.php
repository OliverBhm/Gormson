<?php

namespace App\database\seeds;

class Teammembers
{
    private $teammembers;

    /**
     * @return array
     */
    public function getTeammembers(): array
    {
        return $this->teammembers;
    }

    /**
     * Teammembers constructor.
     * @param $teammembers
     */
    public function __construct()
    {
        $this->teammembers = [
            [
                'first_name' => 'Aye',
                'last_name' => 'Stephen',
            ],
            [
                'first_name' => 'Christopher',
                'last_name' => 'Tohmé',
            ],
            [
                'first_name' => 'Dan',
                'last_name' => 'Bosshammer',
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Marx',
            ],
            [
                'first_name' => 'Emmanouil',
                'last_name' => 'Stafilarakis',
            ],
            [
                'first_name' => 'Ilyes',
                'last_name' => 'Tascou',
            ],
            [
                'first_name' => 'Jacqueline',
                'last_name' => 'Wendel',
            ],
            [
                'first_name' => 'Jens',
                'last_name' => 'Konopka',
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Fink',
            ],
            [
                'first_name' => 'Raphael',
                'last_name' => 'Adam',
            ],
            [
                'first_name' => 'Steven',
                'last_name' => 'Metz',
            ],
            [
                'first_name' => 'Tim',
                'last_name' => 'Gajewsky',
            ],
            [
                'first_name' => 'Oliver',
                'last_name' => 'Böhm',
            ],
        ];
    }
}
