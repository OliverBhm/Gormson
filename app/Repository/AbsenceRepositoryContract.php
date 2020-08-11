<?php

namespace App\Repository;

interface AbsenceRepositoryContract
{
    public function getAll(): object;

    public function create(array $absence): void;

    public function getByName(array $employee): ?int;

    public function currentlyAbsent(): ?object;

    public function absentInDayRange(int $start, int $end): ?object;

    public function absenceUpdated(): ?object;

    public function deleteObsolete(array $events): void;

    public function delete(int $id): bool;
}
