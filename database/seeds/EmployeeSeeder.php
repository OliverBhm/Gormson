<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\database\seeds\Teammembers;


class EmployeeSeeder extends Seeder
{

    public function run()
    {
        $teammembers = new Teammembers();
        $employees = $teammembers->getTeammembers();

        for ($i = 0; $i <= count($employees['first_name']) - 1; $i++) {
            DB::table('employees')->insert([
                'first_name' => $employees['first_name'][$i],
                'last_name' => $employees['last_name'][$i],
            ]);
        }
    }
}
