<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    
    protected $casts = [
        'information' => 'json'
    ];

    public function doctor(){
    	return $this->belongsTo('App\User', 'doctor_id');
    }

    public function patient(){
    	return $this->belongsTo('App\Patient', 'patient_id');
    }

    public function creator(){
    	return $this->belongsTo('App\User', 'creator_id');
    }

    public function petAppointments(){
        return $this->hasMany('App\PetAppointment', 'appointment_id');
    }

    public function reminders(){
        return $this->hasMany('App\AppointmentReminder', 'appointment_id');
    }

    public static function checkSchedAvailability($request){
        $appointment_schedule = date('Y-m-d H:i', strtotime($request->appointment_date_time));

        $appointment = Appointment::where('information->appointment_date_time', '=', $appointment_schedule)
                        ->where('patient_id', $request->patient_id)
                        // ->when($request->doctor, function($query) use ($request){
                        //     $query->where('doctor_id', $request->doctor);
                        // })
                        ->first();

        return $appointment;
    }
}
