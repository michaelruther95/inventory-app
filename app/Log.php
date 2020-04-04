<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	public $action_types = ['create_patient', 'update_patient'];

	protected $casts = [
        'information' => 'json'
    ];

    public static function createLog($payload){
    	$log = new Log();
    	$log->information = $payload;
    	$log->save();
    }

    // --------------------------------------------------------------------
    // ACCESSORS
    // --------------------------------------------------------------------
    public function getInformationAttribute($val){
    	$parsed_value = (array)json_decode($val);
    	$message = $parsed_value['message'];

    	$bold_start = "<strong>";
    	$bold_end = "</strong>";

    	$user_info = \App\User::where('id', $parsed_value['user_id'])->first();
    	$user_profile = $user_info->profile;
    	$message = str_replace(":user", $bold_start.$user_profile['first_name'] . ' ' .$user_profile['last_name'].$bold_end, $message);

    	if($parsed_value['action'] == 'create_patient' || $parsed_value['action'] == 'update_patient' || $parsed_value['action'] == 'delete_patient'){

    		if($parsed_value['action'] == 'create_patient'){
    			$patient = json_decode($parsed_value['record_info']);
    			$patient->information = (array)$patient->information;
    		}
    		else{
    			$patient = \App\Patient::withTrashed()->where('id', $parsed_value['patient_id'])->first();
    		}

    		$message = str_replace(':patient_full_name', $bold_start.$patient->information['first_name'] . ' ' .$patient->information['last_name'].$bold_end, $message);
    	
    		if(isset($parsed_value['orig_value'])){
	    		$parsed_value['orig_value'] = json_decode($parsed_value['orig_value']);
	    		$parsed_value['new_value'] = $patient;
	    	}
    	}

    	if($parsed_value['action'] == 'create_product' || $parsed_value['action'] == 'update_product'){
    		if($parsed_value['action'] == 'create_product'){
    			$product = json_decode($parsed_value['record_info']);
    			$product->information = (array)$product->information;
    		}
    		else{
    			$product = \App\Product::withTrashed()->where('id', $parsed_value['product_id'])->first();
    		}

    		
    		$message = str_replace(':product_name', $bold_start.$product->information['name'].$bold_end, $message);

    		if(isset($parsed_value['orig_value'])){
	    		$parsed_value['orig_value'] = json_decode($parsed_value['orig_value']);
	    		$parsed_value['new_value'] = $product;
	    	}
    	}

    	if($parsed_value['action'] == 'product_restock'){
    		$product = json_decode($parsed_value['record_info']);
    		$product->information = (array)$product->information;
    		$message = str_replace(':product_name', $bold_start.$product->information['name'].$bold_end, $message);
    	}

    	if($parsed_value['action'] == 'create_purchase'){
    		$invoice = json_decode($parsed_value['record_info']);
    		$invoice->information = (array)$invoice->information;
    		$message = str_replace(':invoice_id', $bold_start.$invoice->id.$bold_end, $message);
    	}

	    	
    	$parsed_value['new_message'] = $message;

    	return $parsed_value;
    }
    // --------------------------------------------------------------------
}
