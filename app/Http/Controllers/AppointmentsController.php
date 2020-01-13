<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Appointment;
use Auth;

class AppointmentsController extends Controller
{
    public function create(AppointmentRequest $request){
    	if($request->action == 'create'){
    		$appointment = Appointment::where('information->appointment_date', '=', $request->appointment_date)
    						->where('patient_id', $request->patient_id)
    						->first();
    		if($appointment){
    			return response()->json([
    				'errors' => [
    					'appointment_date' => ['You already have an appointment on the date that you have selected.']
    				]
    			], 422);
    		}


    	}
    	$appointment = new Appointment();
    	$appointment->doctor_id = $request->doctor;
    	$appointment->patient_id = $request->patient_id;
    	$appointment->information = [
    		'appointment_date' => $request->appointment_date,
    		'appointment_time' => $request->appointment_time,
    		'number_of_pets' => $request->number_of_pets,
    	];
    	$appointment->creator_id = Auth::user()->id;
    	$appointment->save();

    	return response()->json([
    		'action' => 'create-appointment',
    		'appointment' => $appointment
    	]);
    }
}
