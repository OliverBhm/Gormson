<?php

namespace App\Repository;

use App\Employees;
use App\Absences;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Class AbsenceRepository
 * @package App\Repository
 */
class AbsencesRepository implements AbsencesRepositoryContract
{

    /**
     * @var Absences
     */
    protected $model;

    /**
     * AbsenceRepository constructor.
     * @param Absences $model
     */
    public function __construct(Absences $model)
    {
        $this->model = $model;
    }

    /**
     * @return object
     */
    public function getAll(): object
    {
        return $this->model->all();
    }

    /**
     * @param array $absence
     */
    public function create(array $absence): void
    {
        Absences::updateOrCreate([
            'absence_id' => $absence["absence_id"]
        ],
            [
                'employee_id' => $this->getByName($absence['employee']),
                'substitute_01_id' => $this->getByName($absence['employee']['substitutes'][0]) ?? Null,
                'substitute_02_id' => $this->getByName($absence['employee']['substitutes'][1]) ?? Null,
                'substitute_03_id' => $this->getByName($absence['employee']['substitutes'][2]) ?? Null,
                'absence_begin' => $absence["absence_begin"],
                'absence_end' => $absence["absence_end"],
                'absence_type' => $absence["employee"]["absence_type"],
            ]);
    }

    /**
     * @param array $employee
     * @return int|null
     */
    public function getByName(array $employee): ?int
    {
        return Cache::rememberForever($employee['first_name'], function () use ($employee) {
            return Employees::where('first_name', $employee['first_name'])
                ->where('last_name', $employee['last_name'])
                ->value('id');
        });
    }

    /**
     * @return object|null
     */
    public function currentlyAbsent(): ?object
    {
        $today = Carbon::now();
        return Absences::where('absence_begin', '<=', $today)
            ->where('absence_end', '>=', $today)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    /**
     * @param int $start
     * @param int $end
     * @return object|null
     */
    public function absentInDayRange(int $start, int $end): ?object
    {
        $startDate = Carbon::now()->addDays($start);
        $endDate = Carbon::now()->addDays($end);
        return Absences::where('absence_begin', '>=', $startDate)
            ->where('absence_begin', '<=', $endDate)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    /**
     * @return object|null
     */
    public function absenceUpdated(): ?object
    {
        $yesterday = Carbon::now()->subDay();
        $week = Carbon::now()->addWeek();
        $lastHour = Carbon::now()->subHour();
        return Absences::where('absence_begin', '>=', $yesterday)
            ->where('absence_begin', '<=', $week)
            ->where('updated_at', '>', $lastHour)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    /**
     * @param array $events
     * @return bool
     */
    public function deleteObsolete(array $events): bool
    {
        $absent = Absences::all();
        $databaseIds = $this->ids($absent);
        $eventIds = $this->ids($events);
        $differentIds = array_diff($databaseIds, $eventIds);
        Absences::whereIn('absence_id', $differentIds)->delete();
        return true;
    }


    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        Absences::getById()->delete($id);
        return true;
    }

    /**
     * @param $array
     * @return array|null
     */
    private function ids($array): ?array
    {
        $ids = [];
        foreach ($array as $item) {
            $ids[] = $item['absence_id'];
        }
        return $ids;
    }

}
