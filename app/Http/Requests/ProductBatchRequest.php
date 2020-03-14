<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductBatchRequest extends FormRequest
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
            'action' => 'required|in:'.implode(',', $allowedActions),
            'product_id' => 'required|exists:products,id',
            'number_of_stocks' => 'required|numeric',
            'expiration_date' => 'required|date|after_or_equal:'.date('Y-m-d'),
            'supplier' => 'required|numeric|exists:suppliers,id',
            'delivery_date' => 'required|date|before_or_equal:'.date('Y-m-d')
        ];

        if(Request::input('action') == 'update'){
            $rules['id'] = 'required|numeric|exists:product_batches,id';
        }

        return $rules;
    }
}
