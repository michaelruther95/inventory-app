<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Patient;
use App\AppointmentReminder;
use Auth;

class AppointmentRemindersController extends Controller
{
    public function create(Request $request){
    	$appointments = Appointment::where('status', 'pending')
    					->where('information->appointment_date_time', '<', date('Y-m-d H:i', strtotime(date('Y-m-d 23:59'). ' +2 days')))
    					->get();

    	$basic  = new \Nexmo\Client\Credentials\Basic(env('NEXMO_KEY'), env('NEXMO_SECRET'));
		$client = new \Nexmo\Client($basic);

    	foreach($appointments as $appointment){
			$patient_info = Patient::where('id', $appointment->patient_id)->first();

			$message = "Reminder For Your appointment date is on ".$appointment->information['appointment_date_time'].".";
			$number = $patient_info->information['phone_number'];

			$message = $client->message()->send([
			    'to' => '63'.$number,
			    'from' => 'CLINIC APPOINTMENT',
			    'text' => $message
			]);

			$appointment_reminder = new AppointmentReminder();
			$appointment_reminder->appointment_id = $appointment->id;
			$appointment_reminder->sender_id = Auth::user()->id;
			$appointment_reminder->date_sent = date('Y-m-d');
			$appointment_reminder->save();
    	}


    	$appointments = Appointment::with('doctor.consultationFee', 'patient', 'creator', 'petAppointments.petInfo.petAppointments.appointmentInfo', 'reminders', 'petAppointments.diseaseFindings.disease', 'petAppointments.petInfo.petAppointments.diseaseFindings.disease')->get();

    	return response()->json([
    		'action' => 'send-reminder',
    		'appointments' => $appointments
    	]);
    }
}
