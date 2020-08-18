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

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
}
