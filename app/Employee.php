<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * @package App
 */
class Employee extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        "first_name",
        "last_name",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leave()
    {
        return $this->hasMany(Employee::class, "id", "employee_id");
    }


}
