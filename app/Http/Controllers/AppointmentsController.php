<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Appointment;
use App\PetAppointment;
use App\Patient;
use App\Disease;
use Auth;

class AppointmentsController extends Controller
{
    public function index(Request $request){
        $check_expired_appointments = Appointment::where('information->appointment_date_time', '<', date('Y-m-d H:i'))
                                    ->where('status', 'pending')
                                    ->get();

        foreach($check_expired_appointments as $expired_appointments){
            $expired_appointments->status = 'expired';
            $expired_appointments->save();
        }

        $appointments = Appointment::with('doctor.consultationFee', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')
                        ->whereHas('petAppointments', function($query){
                            $query->whereHas('petInfo', function($first_inner_query){
                                $first_inner_query->whereHas('petAppointments', function($second_inner_query){
                                    $second_inner_query->where('information->findings', '!=', null);
                                });
                            });
                        })
                        ->get();

        return response()->json([
            'action' => 'get-appointments',
            'appointments' => $appointments,
            'diseases' => Disease::get()
        ]);
    }

    public function create(AppointmentRequest $request){
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
                'findings' => null,
                'prescription' => null
            ];
            $pet_appointment->save();
        }

        $appointment_info = Appointment::with('doctor.consultationFee', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')
                    ->where('id', $appointment->id)
                    ->first();

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

        $appointment_info = Appointment::with('doctor.consultationFee', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')
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

    public function finish(Request $request){
        $appointment_info = Appointment::with('doctor.consultationFee', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')
                            ->where('id', $request->id)
                            ->first();

        $error_msg = null;
        $consultation_fee_error = null;

        foreach($appointment_info->petAppointments as $pet_appointment){
            if(!$pet_appointment->information['findings']){
                $error_msg = "One of the pets of this appointment is still not diagnosed. Please check every pets on this appointment before finishing this appointment.";
            }
        }


        if((int)$request->consultation_fee){
            if((int)$request->consultation_fee <= 0){
                $consultation_fee_error = 'Consultation fee must be greater than 0';
            }
        }
        else{
            $consultation_fee_error = 'Consultation fee must be numeric';
        }


        if($error_msg || $consultation_fee_error){
            return response()->json([
                'error_message' => $error_msg,
                'consultation_fee_error' => $consultation_fee_error
            ], 403);
        }

        $appointment_information = $appointment_info->information;
        $appointment_information['fees'] = [
            'consultation_fee' => $request->consultation_fee
        ];

        $appointment_info->status = 'finished';
        $appointment_info->information = $appointment_information;
        $appointment_info->save();

        return response()->json([
            'appointment_info' => $appointment_info 
        ]);
    }
}
