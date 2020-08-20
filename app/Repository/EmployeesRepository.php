<?php


namespace App\Repository;


use App\Employee;

class EmployeesRepository implements EmployeesRepositoryContract
{
    protected $model;

    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    public function updateOrCreate(array $employee): void
    {
        Employee::updateOrCreate([
            'first_name' => $employee["first_name"],
            'last_name' => $employee["last_name"],
        ]
        );
    }
}
