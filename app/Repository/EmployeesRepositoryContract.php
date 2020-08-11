<?php

namespace App\Repository;

interface EmployeesRepositoryContract
{
    public function create(array $employee): void;
}
