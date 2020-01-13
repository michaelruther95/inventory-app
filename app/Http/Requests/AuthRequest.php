<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AuthRequest extends FormRequest
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
        $allowedActions = ['login', 'forgot-password', 'reset-password'];
        $rules = [
            'action' => 'required|in:'.implode(',', $allowedActions)
        ];

        if(Request::input('action') == 'forgot-password'){
            $rules['email_address'] = 'required|exists:users,email';
        }

        return $rules;
    }
}
