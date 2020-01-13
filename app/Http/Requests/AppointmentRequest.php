<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AppointmentRequest extends FormRequest
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
    public function rules()
    {
        $allowedActions = ['create', 'update', 'cancel'];
        $rules = [
            'action' => 'required|in:'.implode(',', $allowedActions),
            'doctor' => 'required|numeric|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:'.date('Y-m-d'),
            'appointment_time' => 'required',
            'number_of_pets' => 'required|numeric|min:1',
            'patient_id' => 'required|numeric|exists:patients,id',
        ];

        if(Request::input('action') == 'update'){

        }

        return $rules;
    }
}
