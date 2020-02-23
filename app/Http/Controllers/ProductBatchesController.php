<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;
use App\Http\Requests\ProductBatchRequest;
use App\ProductBatch;
use App\Product;

class ProductBatchesController extends Controller
{
	public function create(ProductBatchRequest $request){
        $product_batch = new ProductBatch();
        $product_batch->product_id = $request->product_id;
        $product_batch->information = [
        	'number_of_stocks' => (int)$request->number_of_stocks,
        	'stocks_purchased' => 0,
        	'expiration_date' => date('Y-m-d', strtotime($request->expiration_date)),
        	'supplier' => $request->supplier,
        	'delivery_date' => date('Y-m-d', strtotime($request->delivery_date))
        ];
        $product_batch->save();

        $product = Product::with('batches.purchases')
        			->where('id', $request->product_id)
        			->first();

        return response()->json([
        	'product' => $product
        ]);
	}

	public function update(ProductBatchRequest $request){

	}
}
