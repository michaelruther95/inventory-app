<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiseaseFindingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disease_findings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('disease_id');
            $table->foreign('disease_id')
                  ->references('id')->on('diseases')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('pet_appointment_id');
            $table->foreign('pet_appointment_id')
                  ->references('id')->on('pet_appointments')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('disease_findings');
    }
}
