<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Supplier;
use Validator;

class ProductsController extends Controller
{
    public function show(Request $request){
        $suppliers = Supplier::get();
    	$products = Product::with('batches.supplierInfo', 'purchases')->get();
    	return response()->json([
    		'action' => 'show',
    		'request' => $request->all(),
    		'products' => $products,
            'suppliers' => $suppliers
    	]);
    }

    public function create(Request $request){
        $allowed_types = ['medicine', 'medical supply'];

        $rules = [
            'product_type' => 'required|in:'.implode(',', $allowed_types),
            'name' => 'required|min:3',
            'brand' => 'required|min:3',
            'price' => 'required|numeric'
        ];

        if($request->product_type == 'medicine'){
            $rules['generic_name'] = 'required|min:3';
        }

    	$validator = Validator::make($request->all(), $rules);
    	if($validator->fails()){
    		return response()->json([
    			'errors' => $validator->messages()
    		], 422);
    	}

    	$product = new Product();
    	$product->information = [
            'product_type' => $request->product_type,
    		'name' => $request->name,
    		'generic_name' => $request->generic_name,
    		'brand' => $request->brand,
            'price' => $request->price,
    		'description' => $request->description,
    	];
    	$product->save();
    	$product = Product::with('batches.supplierInfo', 'purchases')->where('id', $product->id)->first();

    	return response()->json([
    		'action' => 'create',
    		'request' => $request->all(),
    		'product' => $product
    	]);
    }

    public function update(Request $request){
    	$validator = Validator::make($request->all(), [
    					'id' => 'required|numeric|exists:products,id',
    					'name' => 'required|min:3',
    					'generic_name' => 'required|min:3',
                        'brand' => 'required|min:3',
                        'price' => 'required|numeric'
    				]);
    	if($validator->fails()){
    		return response()->json([
    			'errors' => $validator->messages()
    		], 422);
    	}

    	$product = Product::where('id', $request->id)->first();
    	$product->information = [
    		'name' => $request->name,
    		'generic_name' => $request->generic_name,
    		'brand' => $request->brand,
            'price' => $request->price,
    		'description' => $request->description,
    	];
    	$product->save();
    	$product = Product::with('batches.supplierInfo', 'purchases')->where('id', $product->id)->first();

    	return response()->json([
    		'action' => 'update',
    		'request' => $request->all(),
    		'product' => $product
    	]);
    }

    public function delete(Request $request){
    	return response()->json([
    		'action' => 'delete',
    		'request' => $request->all()
    	]);
    }
}
