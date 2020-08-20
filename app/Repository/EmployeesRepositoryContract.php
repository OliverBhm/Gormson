<?php

namespace App\Repository;

interface EmployeesRepositoryContract
{
    public function updateOrCreate(array $employee): void;
}
