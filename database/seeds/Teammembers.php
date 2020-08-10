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
            'first_name' => array(
                'Aye',
                'Christopher',
                'Dan',
                'Daniel',
                'Emmanouil',
                'Ilyes',
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
               'Stephen',
               'Tohmé',
               'Bosshammer',
               'Marx',
               'Stafilarakis',
               'Tascou',
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
