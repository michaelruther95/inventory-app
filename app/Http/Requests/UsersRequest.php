<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UsersRequest extends FormRequest
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

        $allowed_actions = ['create', 'update', 'disable'];
        $rules = [];


        if(Request::input('action') == 'disable'){
            $rules = [
                'id' => 'required|numeric|exists:users,id',
                'action' => 'required|in:'.implode(',', $allowed_actions),
            ];

            return $rules;
        }
        else{
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required|in:'.implode(',', ['Male', 'Female']),
                'email' => 'required|email|unique:users,email',
                'action' => 'required|in:'.implode(',', $allowed_actions),
            ];

            if(Request::input('action') == 'create'){
                $rules['account_type'] = 'required|numeric|exists:roles,id';
            }

            // if(Request::input('action') == 'update'){
            //     $rules['id'] = 'required|numeric|exists:users,id';
            // }

            return $rules;
        }
    }
}
