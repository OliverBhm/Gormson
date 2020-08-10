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
                'last_name' => 'Tohmé',
            ],



            'first_name' => array(


                'Jacqueline',
                'Jens',
                'Kevin',
                'Marvin',
                'Raphael',
                'Steven',
                'Tim',
                'Oliver',
            ),
            'last_name' => array(

                'Wendel',
                'Konopka',
                'Fink',
                'Kanitz',
                'Wieczorek',
                'Adam',
                'Metz',
                'Gajewsky',
                'Böhm',
            ),
        ];
    }
}
