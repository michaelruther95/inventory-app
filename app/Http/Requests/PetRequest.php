<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PetRequest extends FormRequest
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
        $allowedActions = ['create', 'update', 'delete'];

        $rules = [
            'action' => 'required|in:'.implode(',', $allowedActions),
            'name' => 'required',
            'type' => 'required',
            'birth_year' => 'required|numeric|min:2000'
        ];

        if(Request::input('action') != 'delete'){
            $rules['patient_id'] = 'required|numeric|exists:patients,id';
        }

        if(Request::input('action') == 'update' || Request::input('action') == 'delete'){
            $rules['id'] = 'required|numeric|exists:pets,id';
        }

        return $rules;
    }
}
