<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pet;
class MedicalRecordsController extends Controller
{
    public function index(Request $request){
    	$pets = Pet::with('petOwner', 'petAppointments.appointmentInfo', 'petAppointments.diseaseFindings.disease')->get();

    	return response()->json([
    		'pets' => $pets
    	]);
    }
}
