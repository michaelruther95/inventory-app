<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\ConsultationFee;
use Auth;

class AccountSettingsRequest extends FormRequest
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
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required|in:'.implode(',', ['Male', 'Female']),
            'email' => 'required|email|unique:users,email,'.Auth::user()->id
        ];

        $consultation_fee_info = ConsultationFee::where('doctor_id', Auth::user()->id)->first();
        if($consultation_fee_info){
            $rules['consultation_fee'] = 'required|numeric|min:1';
        }

        if(Request::input('old_password') || Request::input('new_password') || Request::input('confirm_new_password')){
            $rules['old_password'] = 'required|min:6|old_password';
            $rules['new_password'] = 'required|min:6';
            $rules['confirm_new_password'] = 'required|min:6|same:new_password';
        }

        return $rules;
    }
}
