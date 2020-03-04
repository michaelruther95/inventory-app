<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetAppointment extends Model
{
	use SoftDeletes;

    protected $casts = [
    	'information' => 'json'
    ];

    public function petInfo(){
    	return $this->belongsTo('App\Pet', 'pet_id');
    }

    public function appointmentInfo(){
    	return $this->belongsTo('App\Appointment', 'appointment_id');
    }

    public function diseaseFindings(){
        return $this->hasMany('App\DiseaseFinding', 'pet_appointment_id');
    }
}
