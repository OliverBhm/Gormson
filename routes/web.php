<?php

use Illuminate\Support\Facades\Route;

Route::get('leave', 'VacationController@store');
Route::get('ics', 'IcsDataController@store');
