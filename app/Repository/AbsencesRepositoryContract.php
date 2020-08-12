<?php

namespace App\Repository;

/**
 * Interface AbsenceRepositoryContract
 * @package App\Repository
 */
interface AbsencesRepositoryContract
{
    /**
     * @return object
     */
    public function getAll(): object;

    /**
     * @param array $absence
     */
    public function create(array $absence): void;

    /**
     * @param array $employee
     * @return int|null
     */
    public function getByName(array $employee): ?int;

    /**
     * @return object|null
     */
    public function currentlyAbsent(): ?object;

    /**
     * @param int $start
     * @param int $end
     * @return object|null
     */
    public function absentInDayRange(int $start, int $end): ?object;

    /**
     * @return object|null
     */
    public function absenceUpdated(): ?object;

    /**
     * @param array $events
     */
    public function deleteObsolete(array $events): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
