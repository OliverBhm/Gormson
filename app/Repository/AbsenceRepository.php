<?php

namespace App\Repository;

use App\Employee;
use App\Repository\AbsenceRepositoryContract;
use App\Absence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AbsenceRepository implements AbsenceRepositoryContract
{

    protected $model;

    public function __construct(Absence $model)
    {
        $this->model = $model;
    }

    public function getAll(): object
    {
        return $this->model->all();
    }

    public function create(array $absence): void
    {
        Absence::updateOrCreate([
            'absence_id' => $absence["absence_id"]
        ],
            [
                'employee_id' => $this->getByName($absence['employee']),
                'substitute_01_id' => $this->getByName($absence['employee']['substitutes'][0]),
                'substitute_02_id' => $this->getByName($absence['employee']['substitutes'][1]),
                'substitute_03_id' => $this->getByName($absence['employee']['substitutes'][2]),
                'absence_begin' => $absence["absence_begin"],
                'absence_end' => $absence["absence_end"],
                'absence_type' => $absence["employee"]["absence_type"],
            ]);
    }

    public function getByName(array $employee): ?int
    {
        return Cache::rememberForever($employee['first_name'], function () use ($employee) {
            return Employee::where('first_name', $employee['first_name'])
                ->where('last_name', $employee['last_name'])
                ->value('id');
        });
    }

    public function currentlyAbsent(): ?object
    {
        $today = Carbon::now();
        return Absence::where('absence_begin', '<=', $today)
            ->where('absence_end', '>=', $today)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    public function absentInDayRange(int $start, int $end): ?object
    {
        $startDate = Carbon::now()->addDays($start);
        $endDate = Carbon::now()->addDays($end);
        return Absence::where('absence_begin', '>=', $startDate)
            ->where('absence_begin', '<=', $endDate)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    public function absenceUpdated(): ?object
    {
        $yesterday = Carbon::now()->subDay();
        $week = Carbon::now()->addWeek();
        $lastHour = Carbon::now()->subHour();
        return Absence::where('absence_begin', '>=', $yesterday)
            ->where('absence_begin', '<=', $week)
            ->where('updated_at', '>', $lastHour)
            ->orderBy('absence_begin', 'asc')
            ->get();
    }

    public function deleteObsolete(array $events): void
    {
        $absent = Absence::all();
        $databaseIds = $this->ids($absent);
        $eventIds = $this->ids($events);
        $differentIds = array_diff($databaseIds, $eventIds);
        Absence::whereIn('absence_id', $differentIds)->delete();
    }


    public function delete(int $id): bool
    {
        $this->model->getById()->delete($id);
        return true;
    }

    private function ids($array): ?array
    {
        $ids = [];
        foreach ($array as $item) {
            $ids[] = $item['absence_id'];
        }
        return $ids;
    }

}
