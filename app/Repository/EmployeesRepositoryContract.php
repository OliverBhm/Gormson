<?php

namespace App\Repository;

interface EmployeesRepositoryContract
{
    public function truncate(array $employee): void;
}
