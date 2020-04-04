<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;
use App\Pet;
use App\User;
use App\Http\Requests\PatientRequest;

class PatientsController extends Controller
{
    public function index(Request $request){
    	$patients = Patient::with('pets.petAppointments.appointmentInfo.doctor', 'appointments.petAppointments.petInfo', 'appointments.doctor')->get();
        $doctors = User::with('userRole')->whereHas('userRole', function($query) {
            $query->where('role_id', 1); // 2 = id of the role named 'doctor'
        })->get();

    	return response()->json([
    		'action' => 'get-patients-list',
    		'patients' => $patients,
            'doctors' => $doctors
    	]);
    }

    public function create(PatientRequest $request){
    	$patient = new Patient();
    	$patient->email = $request->email_address;
    	$patient->information = [
    		'first_name' => $request->first_name,
    		'last_name' => $request->last_name,
    		'email_address' => $request->email_address,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
    	];
    	$patient->save();

        $patient = Patient::with('pets.petAppointments.appointmentInfo.doctor', 'appointments.petAppointments.petInfo', 'appointments.doctor')
                    ->where('id', $patient->id)
                    ->first();

        $new_log = \App\Log::createLog([
            'action' => 'create_patient',
            'patient_id' => $patient->id,
            'user_id' => auth()->user()->id,
            'message' => ':user created a new patient named :patient_full_name',
            'record_info' => json_encode($patient)
        ]);

    	return response()->json([
    		'action' => 'create-patient',
    		'patient_info' => $patient
    	]);
    }

    public function update(PatientRequest $request){
    	$patient = Patient::find($request->id);
        $orig_value = Patient::where('id', $request->id)->first();

    	$patient->email = $request->email_address;
    	$patient->information = [
    		'first_name' => $request->first_name,
    		'last_name' => $request->last_name,
    		'email_address' => $request->email_address,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
    	];
    	$patient->save();

        $patient = Patient::with('pets.petAppointments.appointmentInfo.doctor', 'appointments.petAppointments.petInfo', 'appointments.doctor')
                    ->where('id', $patient->id)
                    ->first();

        $new_log = \App\Log::createLog([
            'action' => 'update_patient',
            'patient_id' => $patient->id,
            'user_id' => auth()->user()->id,
            'message' => ':user updated patient named :patient_full_name',
            'orig_value' => json_encode($orig_value) 
        ]);

    	return response()->json([
    		'action' => 'update',
    		'patient_info' => $patient
    	]);
    }

    public function delete(Request $request){
    	$patient = Patient::withTrashed()->where('id', $request->id)->delete();
        $patient = Patient::withTrashed()->where('id', $request->id)->first();

        $new_log = \App\Log::createLog([
            'action' => 'delete_patient',
            'patient_id' => $patient->id,
            'user_id' => auth()->user()->id,
            'message' => ':user deleted the patient named :patient_full_name'
        ]);

    	return response()->json([
    		'message' => 'patient successfully removed from your records'
    	]);
    }
}
