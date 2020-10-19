<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private $calenderResultWithoutSubstitutes;

    public function test__construct()
    {
        $this->calenderResultWithoutSubstitutes = [
            "employee" => "Marvin Kanitz",
            "substitutes" => null,
            "absence_type" => "Freizeitausgleich",
            "days" => "1,0",
            'absence_begin' => now()->addDay(),
            'absence_end'
        ];
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        //$this->assertEquals();
    }
}
