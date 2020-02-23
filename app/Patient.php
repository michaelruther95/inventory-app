<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
	use SoftDeletes;

    protected $casts = [
        'information' => 'json'
    ];

    public function pets(){
    	return $this->hasMany('App\Pet', 'patient_id');
    }

    public function appointments(){
    	return $this->hasMany('App\Appointment', 'patient_id');
    }
}
