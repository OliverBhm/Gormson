<?php


namespace App\Repository;


use App\Employee;
use Illuminate\Support\Facades\DB;

class EmployeesRepository implements EmployeesRepositoryContract
{
    protected $model;

    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    public function truncate(array $employee): void
    {
        Employee::truncate();
        Employee::insert($employee);
    }
}
