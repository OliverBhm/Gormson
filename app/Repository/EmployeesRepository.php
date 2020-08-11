<?php


namespace App\Repository;


use App\Employees;

class EmployeesRepository implements EmployeesRepositoryContract
{
    protected $model;

    public function __construct(Employees $model)
    {
        $this->model = $model;
    }

    public function create(array $employee): void
    {
        Employees::updateOrCreate([
            'first_name' => $employee["first_name"],
            'last_name' => $employee["last_name"],
        ]
        );
    }
}
