<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\database\seeds\Teammembers;


class EmployeeSeeder extends Seeder
{

    public function run()
    {
        $teammembers = new Teammembers();
        $employees = $teammembers->getTeammembers();

        Cache::flush();
        foreach ($employees as $employee) {
            DB::table('employees')->insert([
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
            ]);
        }
    }
}
