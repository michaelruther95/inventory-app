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
    	$products = Product::getAllProducts();

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
        $product->is_selected = false;
        $product->stocks_to_buy = 0;
        $product->validation_error = '';

        $new_log = \App\Log::createLog([
            'action' => 'create_product',
            'product_id' => $product->id,
            'user_id' => auth()->user()->id,
            'message' => ':user created a new product named :product_name',
            'record_info' => json_encode($product)
        ]);

    	return response()->json([
    		'action' => 'create',
    		'request' => $request->all(),
    		'product' => $product
    	]);
    }

    public function update(Request $request){
        $allowed_types = ['medicine', 'medical supply'];

        $rules = [
            'id' => 'required|numeric|exists:products,id',
            'product_type' => 'required|in:'.implode(',', $allowed_types),
            'name' => 'required|min:3',
            'generic_name' => 'required|min:3',
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

    	$product = Product::where('id', $request->id)->first();
        $orig_value = Product::where('id', $request->id)->first();

    	$product->information = [
            'product_type' => $request->product_type,
    		'name' => $request->name,
    		'generic_name' => $request->generic_name,
    		'brand' => $request->brand,
            'price' => $request->price,
    		'description' => $request->description,
    	];
    	$product->save();

        $product = Product::getProductInfo($product->id);

        $new_log = \App\Log::createLog([
            'action' => 'update_product',
            'product_id' => $product->id,
            'user_id' => auth()->user()->id,
            'message' => ':user updated a product named :product_name',
            'orig_value' => json_encode($orig_value) 
        ]);

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
