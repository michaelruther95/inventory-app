<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PetRequest;
use App\Pet;
use App\Patient;

class PetsController extends Controller
{
    public function create(PetRequest $request){
    	$pet = new Pet();
    	$pet->patient_id = $request->patient_id;
    	$pet->information = [
    		'name' => $request->name,
    		'type' => $request->type,
    		'birth_year' => $request->birth_year,
    		'description' => $request->description,
    	];
    	$pet->save();

    	return response()->json([
    		'requests' => $request->all(),
    		'pet' => $pet
    	]);
    }

    public function update(PetRequest $request){
        $pet_info = Pet::where('id', $request->id)->first();
        $pet_info->information = [
            'name' => $request->name,
            'type' => $request->type,
            'birth_year' => $request->birth_year,
            'description' => $request->description,
        ];
        $pet_info->save();

        $patient = Patient::with('pets.petAppointments.appointmentInfo.doctor', 'appointments.petAppointments.petInfo', 'appointments.doctor')
                    ->where('id', $pet_info->patient_id)
                    ->first();

    	return response()->json([
    		'requests' => $request->all(),
            'pet' => $pet_info,
            'patient' => $patient
    	]);
    }

    public function delete(PetRequest $request){
    	return response()->json([
    		'requests' => $request->all()
    	]);
    }
}
