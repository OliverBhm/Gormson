<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Absence
 * @package App
 */
class Absence extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        "employee_id",
        "absence_id",
        "absence_begin",
        "absence_end",
        "absence_type",
        'last_modified',
        "substitute_01_id",
        "substitute_02_id",
        "substitute_03_id",
    ];

    /**
     * @var array
     */
    protected $casts = [
        'absence_begin' => 'date:D M d, Y',
        'absence_end' => 'date:D M d, Y',
    ];

    /**
     * @var string
     */
    public $table = "absences";


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function substitute01()
    {
        return $this->hasOne(Employee::class, "id", "substitute_01_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function substitute02()
    {
        return $this->hasOne(Employee::class, "id", "substitute_02_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function substitute03()
    {
        return $this->hasOne(Employee::class, "id", "substitute_03_id");
    }

}
