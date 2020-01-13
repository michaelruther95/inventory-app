<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')
                  ->references('id')->on('patients')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->longText('information');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
