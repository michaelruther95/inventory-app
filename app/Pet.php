<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
	use SoftDeletes;

    protected $casts = [
        'information' => 'json'
    ];

    public function petAppointments(){
    	return $this->hasMany('App\PetAppointment', 'pet_id');
    }

    public function petOwner(){
    	return $this->belongsTo('App\Patient', 'patient_id');
    }
}
