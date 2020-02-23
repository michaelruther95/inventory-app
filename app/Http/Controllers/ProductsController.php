<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Validator;

class ProductsController extends Controller
{
    public function show(Request $request){
    	$products = Product::with('batches.purchases')->get();
    	return response()->json([
    		'action' => 'show',
    		'request' => $request->all(),
    		'products' => $products
    	]);
    }

    public function create(Request $request){
    	$validator = Validator::make($request->all(), [
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

    	$product = new Product();
    	$product->information = [
    		'name' => $request->name,
    		'generic_name' => $request->generic_name,
    		'brand' => $request->brand,
            'price' => $request->price,
    		'description' => $request->description,
    	];
    	$product->save();
    	$product = Product::with('batches.purchases')->where('id', $product->id)->first();

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
    	$product = Product::with('batches.purchases')->where('id', $product->id)->first();

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
