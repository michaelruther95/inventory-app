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
        $allowed_actions = ['create', 'update', 'cancel'];
        $rules = [
            'action' => 'required|in:'.implode(',', $allowed_actions),
        ];

        if(Request::input('action') == 'create'){
            $rules['doctor'] = 'required|numeric|exists:users,id';
            $rules['appointment_date_time'] = 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d H:i:s');
            $rules['patient_id'] = 'required|numeric|exists:patients,id';
            $rules['selected_pets.*.id'] = 'required|numeric|exists:pets,id';
            $rules['selected_pets.*.reason'] = 'required';
        }

        if(Request::input('action') == 'update'){
            $rules['appointment_date_time'] = 'required|date_format:Y-m-d H:i:s|after_or_equal:'.date('Y-m-d H:i:s');
            $rules['record_id'] = 'required|numeric|exists:appointments,id';
        }

        return $rules;
    }
}
