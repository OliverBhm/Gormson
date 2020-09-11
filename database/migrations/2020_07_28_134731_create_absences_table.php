<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsencesTable extends Migration
{
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('absence_id');
            $table->date('absence_begin');
            $table->date('absence_end');
            $table->string('absence_type');
            $table->integer('substitute_01_id')->nullable();
            $table->integer('substitute_02_id')->nullable();
            $table->integer('substitute_03_id')->nullable();
            $table->dateTime('last_modified');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absences');
    }
}

