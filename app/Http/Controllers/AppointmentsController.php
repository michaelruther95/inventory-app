<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Appointment;
use App\PetAppointment;
use App\Patient;
use Auth;

class AppointmentsController extends Controller
{
    public function index(Request $request){
        $check_expired_appointments = Appointment::where('information->appointment_date_time', '<', date('Y-m-d H:i'))->get();
        foreach($check_expired_appointments as $expired_appointments){
            $expired_appointments->status = 'expired';
            $expired_appointments->save();
        }

        $appointments = Appointment::with('doctor', 'patient', 'creator', 'petAppointments')->get();

        return response()->json([
            'action' => 'get-appointments',
            'appointments' => $appointments
        ]);
    }

    public function create(AppointmentRequest $request){
    	if($request->action == 'create'){
    		$appointment = Appointment::checkSchedAvailability($request);
    		if($appointment){
    			return response()->json([
    				'errors' => [
    					'appointment_date_time' => ['This time is no longer available. Please select a different time.']
    				]
    			], 422);
    		}
    	}

    	$appointment = new Appointment();
    	$appointment->doctor_id = $request->doctor;
    	$appointment->patient_id = $request->patient_id;
    	$appointment->information = [
    		'appointment_date_time' => date('Y-m-d H:i', strtotime($request->appointment_date_time)),
            'text_reminders' => [],
            'reschedules' => []
    	];
    	$appointment->creator_id = Auth::user()->id;
    	$appointment->save();

        foreach($request->selected_pets as $pet){
            $pet_appointment = new PetAppointment();
            $pet_appointment->appointment_id = $appointment->id;
            $pet_appointment->pet_id = $pet['id'];
            $pet_appointment->information = [
                'reason' => $pet['reason'],
                'findings' => null
            ];
            $pet_appointment->save();
        }

        $appointment_info = Appointment::with('petAppointments.petInfo', 'doctor')->where('id', $appointment->id)->first();
        $patient = Patient::with('pets.petAppointments.appointmentInfo.doctor', 'appointments.petAppointments.petInfo', 'appointments.doctor')
                    ->where('id', $request->patient_id)
                    ->first();

    	return response()->json([
    		'action' => 'create-appointment',
    		'appointment' => $appointment_info,
            'patient' => $patient
    	]);
    }

    public function update(AppointmentRequest $request){
        $appointment = Appointment::checkSchedAvailability($request);
        if($appointment){
            return response()->json([
                'errors' => [
                    'appointment_date_time' => ['This time is no longer available. Please select a different time.']
                ]
            ], 422);
        }

        $appointment_info = Appointment::where('id', $request->record_id)->first();
        if(!$appointment_info){
            return response()->json([
                'error' => 'The appointment record does not exist anymore. Please refresh the page for an updated record.'
            ], 404);
        }

        $app_info = $appointment_info->information;
        $reschedule_list = $app_info['reschedules'];
        $new_reschedule_record = [
            'old' => $app_info['appointment_date_time'],
            'new' => date('Y-m-d H:i', strtotime($request->appointment_date_time))
        ];
        array_push($reschedule_list, $new_reschedule_record);

        $app_info['appointment_date_time'] = date('Y-m-d H:i', strtotime($request->appointment_date_time));
        $app_info['reschedules'] = $reschedule_list;

        $appointment_info->information = $app_info;
        $appointment_info->save();

        $appointment_info = Appointment::with('doctor', 'patient', 'creator', 'petAppointments')
                            ->where('id', $appointment_info->id)
                            ->first();

        return response()->json([
            'action' => 'reschedule-appointment',
            'request' => $request->all(),
            'appointment_info' => $appointment_info
        ]);
    }

    public function delete(Request $request){
        $appointment_info = Appointment::where('id', $request->record_id)->first();
        if(!$appointment_info){
            return response()->json([
                'error' => 'The appointment record does not exist anymore. Please refresh the page for an updated record.'
            ], 404);
        }

        $appointment_info->status = 'cancelled';
        $appointment_info->save();

        return response()->json([
            'action' => 'delete-appointment',
            'request' => $request->all()
        ]);
    }
}
