<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductBatch;
use App\Purchase;
use App\PurchaseBatch;
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

		$product_info = Product::where('id', $request->product_id)->first();

		$product_batches = ProductBatch::where('product_id', $request->product_id)
						->where('information->is_still_available', '=', true)
						->where('information->expiration_date', '>' ,date('Y-m-d'))
						->get();

		if(count($product_batches) <= 0){
			return response()->json([
				'errors' => [
					'stocks_to_buy' => [
						'No more enough stocks to buy base on the amount of stocks you have entered.'
					]
				],

				'product_batches' => $product_batches
			], 422);
		}

		$batches_to_be_emptied = [];
		$final_batch_to_be_purchased = null;
		$current_stocks_count_to_get = 0;

		// ------------------------------------------------------------------------------------
		// CHECKS EVERY BATCHES THAT IS STILL AVAILABLE AND NOT EXPIRED
		foreach($product_batches as $product_batch){
			$number_of_stocks = $product_batch->information['number_of_stocks'];
			$stocks_purchased = $product_batch->information['stocks_purchased'];

			$batch_remaining_stocks = $number_of_stocks - $stocks_purchased;

			if($current_stocks_count_to_get < $request->stocks_to_buy){
				$stocks_remaining_to_add = $request->stocks_to_buy - $current_stocks_count_to_get;

				if($batch_remaining_stocks <= $stocks_remaining_to_add){
					array_push($batches_to_be_emptied, $product_batch->id);
					$current_stocks_count_to_get += $batch_remaining_stocks;
				}
				else{
					$stocks_remaining_to_add = $request->stocks_to_buy - $current_stocks_count_to_get;
					$final_batch_to_be_purchased = [
						'batch_id' => $product_batch->id,
						'stock_will_remain' => $batch_remaining_stocks - ($request->stocks_to_buy - $current_stocks_count_to_get),
						'stocks_remaining_to_add' => $stocks_remaining_to_add
					];

					$current_stocks_count_to_get += $stocks_remaining_to_add;
				}
			}

		}

		if($current_stocks_count_to_get < $request->stocks_to_buy){
			return response()->json([
				'errors' => [
					'stocks_to_buy' => [
						'No more enough stocks to buy base on the amount of stocks you have entered.'
					]
				],
				'from' => '2nd Conditional Guard'
			], 422);
		}

		$new_purchase = new Purchase();
		$new_purchase->creator_id = Auth::user()->id;
		$new_purchase->product_id = $request->product_id;
		$new_purchase->information = [
			'stocks_purchase' => $request->stocks_to_buy,
			'price_per_stock' => $product_info->information['price'],
			'total' => $product_info->information['price'] * $request->stocks_to_buy
		];
		$new_purchase->save();
			
		for($counter = 0; $counter < count($batches_to_be_emptied); $counter++){
			$batch_info = ProductBatch::where('id', $batches_to_be_emptied[$counter])->first();
			$batch_information = $batch_info->information;
			$batch_information['is_still_available'] = false;
			$batch_information['stocks_purchased'] = $batch_information['number_of_stocks'];

			$remaining_stocks = $batch_information['remaining_stocks'];
			unset($batch_information['remaining_stocks']);
			$batch_info->information = $batch_information;
			$batch_info->save();


			$purchase_batch = new PurchaseBatch();
			$purchase_batch->purchase_id = $new_purchase->id;
			$purchase_batch->product_batch_id = $batch_info->id;
			$purchase_batch->information = [
				'stocks_purchased' => $remaining_stocks
			];
			$purchase_batch->save();
		}

		if($final_batch_to_be_purchased){
			$batch_info = ProductBatch::where('id', $final_batch_to_be_purchased['batch_id'])->first();
			$batch_information = $batch_info->information;

			$batch_information['is_still_available'] = true;
			if($final_batch_to_be_purchased['stock_will_remain'] <= 0){
				$batch_information['is_still_available'] = false;
			}
			
			$batch_information['stocks_purchased'] = $batch_information['stocks_purchased'] + $final_batch_to_be_purchased['stocks_remaining_to_add'];
			unset($batch_information['remaining_stocks']);
			$batch_info->information = $batch_information;
			$batch_info->save();


			$purchase_batch = new PurchaseBatch();
			$purchase_batch->purchase_id = $new_purchase->id;
			$purchase_batch->product_batch_id = $batch_info->id;
			$purchase_batch->information = [
				'stocks_purchased' => $final_batch_to_be_purchased['stocks_remaining_to_add']
			];
			$purchase_batch->save();
		}

		$product = Product::with('batches', 'purchases')
        			->where('id', $request->product_id)
        			->first();

		return response()->json([
			'product' => $product
		]);
    }
}
