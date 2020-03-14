<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use Validator;

class SuppliersController extends Controller
{
    public function index(Request $request){
        return response()->json([
            'suppliers' => Supplier::with('productSuppliers.productInfo')->get()
        ]);
    }

    public function create(Request $request){
    	$validator = Validator::make($request->all(), [
    					'supplier_name' => 'required',
    					'contact_number' => 'required|min:10|max:10',
    				]);

    	if($validator->fails()){
    		return response()->json([
    			'errors' => $validator->messages()
    		], 422);
    	}

    	$new_supplier = new Supplier();
    	$new_supplier->information = [
    		'supplier_name' => $request->supplier_name,
    		'contact_number' => $request->contact_number
    	];
    	$new_supplier->save();

    	return response()->json([
    		'suppliers' => Supplier::with('productSuppliers.productInfo')->get()
    	]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
                        'id' => 'required|numeric|exists:suppliers,id',
                        'supplier_name' => 'required',
                        'contact_number' => 'required|min:10|max:10',
                    ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->messages()
            ], 422);
        }

        $supplier_info = Supplier::where('id', $request->id)->first();

        if($supplier_info){
            $supplier_info->information = [
                'supplier_name' => $request->supplier_name,
                'contact_number' => $request->contact_number
            ];
            $supplier_info->save();
        }

        return response()->json([
            'suppliers' => Supplier::with('productSuppliers.productInfo')->get()
        ]);
    }
}
