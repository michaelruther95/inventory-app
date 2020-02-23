<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductBatch;
use App\Purchase;
use App\Product;
use Validator;
use Auth;

class PurchasesController extends Controller
{
    public function create(Request $request){
		$validator = Validator::make($request->all(), [
						'product_id' => 'required|numeric|exists:products,id',
						'stocks_to_buy' => 'required|numeric',
						'sold_to' => 'required'
					]);
		
		if($validator->fails()){
			return response()->json([
				'errors' => $validator->messages()
			], 422);
		}

		$product_batch = ProductBatch::where('product_id', $request->product_id)
						->whereColumn('information->number_of_stocks', '>', 'information->stocks_purchased')
						->first();

		if(!$product_batch){
			return response()->json([
				'errors' => [
					'stocks_to_buy' => [
						'No more enough stocks to buy base on the amount of stocks you have entered.'
					]
				]
			], 422);
		}

		$remaining_stocks = $product_batch->information['number_of_stocks'] - $request->stocks_to_buy;
		if($remaining_stocks < 0){
			return response()->json([
				'errors' => [
					'stocks_to_buy' => [
						'No more enough stocks to buy base on the amount of stocks you have entered.'
					]
				]
			], 422);
		}

		$product_info = Product::where('id', $request->product_id)->first();


		$purchase = new Purchase();
		$purchase->creator_id = Auth::user()->id;
		$purchase->product_id = $request->product_id;
		$purchase->product_batch_id = $product_batch->id;
		$purchase->information = [
			'total' => (float)$product_info->information['price'] * $request->stocks_to_buy,
			'quantity' => (int)$request->stocks_to_buy,
			'price_per_stock' => (float)$product_info->information['price'],
			'sold_to' => $request->sold_to
		];
		$purchase->save();

		$current_info = $product_batch->information;
		unset($current_info['is_expired']);
		unset($current_info['remaining_stocks']);
		$current_info['stocks_purchased'] = $product_batch->information['stocks_purchased'] + $request->stocks_to_buy;

		$product_batch->information = $current_info;
		$product_batch->save();

		$product = Product::with('batches.purchases')
        			->where('id', $request->product_id)
        			->first();

		return response()->json([
			'product_batch' => $product_batch,
			'product' => $product
		]);
    }
}
