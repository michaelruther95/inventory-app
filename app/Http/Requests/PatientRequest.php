<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PatientRequest extends FormRequest
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
        $allowedActions = ['create', 'update'];
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email_address' => 'required|email|unique:patients,email',
            'action' => 'required|in:'.implode(',', $allowedActions)
        ];

        // dd(Request::input('id'));

        if(Request::input('action') == 'update'){
            $rules['id'] = 'required|exists:patients';
            $rules['email_address'] = 'required|email|unique:patients,email,'.Request::input('id');
        }
        
        return $rules;
    }
}
