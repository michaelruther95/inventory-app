<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pet;
use App\DownloadKey;
use PDF;
use App\Helpers\RandomStringGeneratorHelper;

class MedicalRecordsController extends Controller
{
    public function index(Request $request){
    	$pets = Pet::with('petOwner', 'petAppointments.appointmentInfo', 'petAppointments.diseaseFindings.disease')->get();

    	return response()->json([
    		'pets' => $pets
    	]);
    }

    public function download(Request $request){
    	if($request->create_download_key){
    		if($request->record_type == 'pet_record'){
    			$delete_current_record = DownloadKey::where('information->record_type', 'pet_record')
										->where('information->record_id', $request->record_id)
										->delete();

    			$download_key = RandomStringGeneratorHelper::generateRandomString(30);

    			$download_record = new DownloadKey();
    			$download_record->information = [
    				'record_type' => 'pet_record',
    				'record_id' => $request->record_id,
    				'download_key' => $download_key
    			];
    			$download_record->save();

    			return response()->json([
    				'download_key' => $download_key
    			]);
    		}
    	}
    	else{
    		$download_record = DownloadKey::where('information->download_key', $request->download_key)->first();
    		if($download_record){
    			if($download_record->information['record_type'] == 'pet_record'){
    				$pet_info = Pet::with('petOwner', 'petAppointments.appointmentInfo', 'petAppointments.diseaseFindings.disease')
			    			->where('id', $download_record->information['record_id'])
			    			->first();

			    	if($pet_info){
			    		$pdf = PDF::loadView('pdfs.medical-records', $pet_info);

			    		$file_name = strtotime(date('Y-m-d H:i:s'));

			    		$delete_record = DownloadKey::where('information->download_key', $request->download_key)->delete();

				    	return $pdf->download($file_name.'.pdf');
			    	}
			    	else{
			    		return "Invalid Record";
			    	}
    			}
    		}
    		else{
    			return "Invalid Record";
    		}
    	}
    }
}
