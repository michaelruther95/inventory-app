<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PetAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {

        $rules = [
            'pet_appointments.*.appointment_id' => 'required|numeric|exists:appointments,id',
            'pet_appointments.*.pet_appointment_id' => 'required|numeric|exists:pet_appointments,id',
            'pet_appointments.*.findings' => 'required',
            'pet_appointments.*.prescription' => 'required'
        ];


        for($counter = 0; $counter < count($request->pet_appointments); $counter++){
            if(!$request->pet_appointments[$counter]['no_disease_found']){
                $rules['pet_appointments.'.$counter.'.disease_list'] = 'required|array|min:1';
            }
        }

        return $rules;

        // return [
        //     'pet_appointments.*.appointment_id' => 'required|numeric|exists:appointments,id',
        //     'pet_appointments.*.pet_appointment_id' => 'required|numeric|exists:pet_appointments,id',
        //     'pet_appointments.*.findings' => 'required',
        //     'pet_appointments.*.prescription' => 'required',

        //     'pet_appointments.*.disease_list' => 'required|array|min:1'
        // ];
    }
}
