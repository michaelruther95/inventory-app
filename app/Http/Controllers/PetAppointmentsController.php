<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetAppointmentRequest;
use App\Appointment;
use App\PetAppointment;
use App\Disease;
use App\DiseaseFinding;

class PetAppointmentsController extends Controller
{
    public function update(PetAppointmentRequest $request){
    	for($counter = 0; $counter < count($request->pet_appointments); $counter++){
    		$pet_appointment = PetAppointment::where('appointment_id', $request->pet_appointments[$counter]['appointment_id'])
    								->where('id', $request->pet_appointments[$counter]['pet_appointment_id'])
    								->first();


    		$delete_disease_findings = DiseaseFinding::where('pet_appointment_id', $pet_appointment->id)->delete();

    		if($pet_appointment){
    			$pet_appointment_info = $pet_appointment->information;
    			$pet_appointment_info['findings'] = $request->pet_appointments[$counter]['findings'];
    			$pet_appointment_info['prescription'] = $request->pet_appointments[$counter]['prescription'];
    			$pet_appointment_info['no_disease_found'] = $request->pet_appointments[$counter]['no_disease_found'];


    			if(!$request->pet_appointments[$counter]['no_disease_found']){
    				$disease_idents_to_add = [];
	    			for($disease_counter = 0; $disease_counter < count($request->pet_appointments[$counter]['disease_list']); $disease_counter++){
	    				$current_disease_name = $request->pet_appointments[$counter]['disease_list'][$disease_counter];

	    				$check_disease = Disease::where('disease_name', strtolower($current_disease_name))->first();
	    				if($check_disease){
	    					array_push($disease_idents_to_add, $check_disease->id);
	    				}
	    				else{
	    					$new_disease = New Disease();
	    					$new_disease->disease_name = strtolower($current_disease_name);
	    					$new_disease->save();

	    					array_push($disease_idents_to_add, $new_disease->id);
	    				}
	    			}

	    			for($disease_ident_counter = 0; $disease_ident_counter < count($disease_idents_to_add); $disease_ident_counter++){
	    				$disease_finding = new DiseaseFinding();
	    				$disease_finding->disease_id = $disease_idents_to_add[$disease_ident_counter];
	    				$disease_finding->pet_appointment_id = $pet_appointment->id;
	    				$disease_finding->save();
	    			}
    			}	

    			$pet_appointment->information = $pet_appointment_info;
    			$pet_appointment->save();
    		}
    	}

    	$appointment_info = Appointment::with('doctor', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')
                        ->whereHas('petAppointments', function($query){
                            $query->whereHas('petInfo', function($first_inner_query){
                                $first_inner_query->whereHas('petAppointments', function($second_inner_query){
                                    $second_inner_query->where('information->findings', '!=', null);
                                });
                            });
                        })
                        ->where('id', $request->appointment_id)
                        ->first();

    	return response()->json([
    		'action' => 'submit-doctor-findings',
    		'request' => $request->all(),
    		'appointment_info' => $appointment_info
    	]);
    }
}
