<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseBatch;
use App\ProductBatch;
use App\Invoice;
use App\InvoicePurchase;
use App\Product;
use App\Purchase;
use Auth;
use Validator;

class MultiplePurchaseController extends Controller
{
	public function create(Request $request){
		$validator = Validator::make($request->all(), [
						'sold_to' => 'required'
					]);

		if($validator->fails()){
			return response()->json([
				'errors' => $validator->messages(),
				'reason' => 'validator'
			], 422);
		}


		$product_results = [];
		$list_has_validations = false;
		for($counter = 0; $counter < count($request->product_list); $counter++){

			$product_batches = ProductBatch::where('product_id', $request->product_list[$counter]['product_id'])
							->where('information->is_still_available', '=', true)
							->where('information->expiration_date', '>' ,date('Y-m-d'))
							->get();
			
			$product_object = [
				'product_id' => $request->product_list[$counter]['product_id'],
				'validation_error' => '',
				'batches_to_be_emptied' => null,
				'final_batch_to_be_purchased' => null,
				'stocks_to_buy' => $request->product_list[$counter]['stocks_to_buy']
			];

			if(count($product_batches) <= 0){
				$product_object['validation_error'] = 'No more enough stocks to buy base on the amount of stocks you have entered.';
				$list_has_validations = true;
			}
			else{
				$batches_to_be_emptied = [];
				$final_batch_to_be_purchased = null;
				$current_stocks_count_to_get = 0;


				// ------------------------------------------------------------------------------------
				// CHECKS EVERY BATCHES THAT IS STILL AVAILABLE AND NOT EXPIRED
				foreach($product_batches as $product_batch){
					$number_of_stocks = $product_batch->information['number_of_stocks'];
					$stocks_purchased = $product_batch->information['stocks_purchased'];

					$batch_remaining_stocks = $number_of_stocks - $stocks_purchased;

					if($current_stocks_count_to_get < $request->product_list[$counter]['stocks_to_buy']){
						$stocks_remaining_to_add = $request->product_list[$counter]['stocks_to_buy'] - $current_stocks_count_to_get;

						if($batch_remaining_stocks <= $stocks_remaining_to_add){
							array_push($batches_to_be_emptied, $product_batch->id);
							$current_stocks_count_to_get += $batch_remaining_stocks;
						}
						else{
							$stocks_remaining_to_add = $request->product_list[$counter]['stocks_to_buy'] - $current_stocks_count_to_get;
							$final_batch_to_be_purchased = [
								'batch_id' => $product_batch->id,
								'stock_will_remain' => $batch_remaining_stocks - ($request->product_list[$counter]['stocks_to_buy'] - $current_stocks_count_to_get),
								'stocks_remaining_to_add' => $stocks_remaining_to_add
							];

							$current_stocks_count_to_get += $stocks_remaining_to_add;
						}
					}
				}

				$product_object['batches_to_be_emptied'] = $batches_to_be_emptied;
				$product_object['final_batch_to_be_purchased'] = $final_batch_to_be_purchased;

				if($current_stocks_count_to_get < $request->product_list[$counter]['stocks_to_buy']){
					$product_object['validation_error'] = 'No more enough stocks to buy base on the amount of stocks you have entered.';
					$list_has_validations = true;
				}
			}

			array_push($product_results, $product_object);
		}

				
		if($list_has_validations == true){
			$products = Product::getAllProducts();
			foreach($products as $product){
				for($counter = 0; $counter < count($product_results); $counter++){
					if($product->id == $product_results[$counter]['product_id']){
						$product->validation_error = $product_results[$counter]['validation_error'];
						$product->is_selected = true;
						$product->stocks_to_buy = $product_results[$counter]['stocks_to_buy'];
					}
				}
			}

			return response()->json([
				'action' => 'create-multiple-purchase-product',
				'request' => $request->all(),
				'product_results' => $product_results,
				'products' => $products,
				'reason' => 'stock_checking'
			], 422);
		}

		// return response()->json([
		// 	'action' => 'create-multiple-purchase-product',
		// 	'request' => $request->all(),
		// 	'product_results' => $product_results
		// ]);


		$purchase_id_list = [];
		for($counter = 0; $counter < count($product_results); $counter++){
			$product_info = Product::where('id', $product_results[$counter]['product_id'])->first();

			$new_purchase = new Purchase();
			$new_purchase->creator_id = Auth::user()->id;
			$new_purchase->product_id = $product_results[$counter]['product_id'];
			$new_purchase->information = [
				'stocks_purchase' => $product_results[$counter]['stocks_to_buy'],
				'price_per_stock' => $product_info->information['price'],
				'total' => $product_info->information['price'] * $product_results[$counter]['stocks_to_buy'],
				'sold_to' => $request->sold_to
			];
			$new_purchase->save();
			array_push($purchase_id_list, $new_purchase->id);
				
			for($batch_counter = 0; $batch_counter < count($product_results[$counter]['batches_to_be_emptied']); $batch_counter++){
				$batch_info = ProductBatch::where('id', $product_results[$counter]['batches_to_be_emptied'][$batch_counter])->first();
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

			if($product_results[$counter]['final_batch_to_be_purchased']){
				$batch_info = ProductBatch::where('id', $product_results[$counter]['final_batch_to_be_purchased']['batch_id'])->first();
				$batch_information = $batch_info->information;

				$batch_information['is_still_available'] = true;
				if($product_results[$counter]['final_batch_to_be_purchased']['stock_will_remain'] <= 0){
					$batch_information['is_still_available'] = false;
				}
				
				$batch_information['stocks_purchased'] = $batch_information['stocks_purchased'] + $product_results[$counter]['final_batch_to_be_purchased']['stocks_remaining_to_add'];
				unset($batch_information['remaining_stocks']);
				$batch_info->information = $batch_information;
				$batch_info->save();


				$purchase_batch = new PurchaseBatch();
				$purchase_batch->purchase_id = $new_purchase->id;
				$purchase_batch->product_batch_id = $batch_info->id;
				$purchase_batch->information = [
					'stocks_purchased' => $product_results[$counter]['final_batch_to_be_purchased']['stocks_remaining_to_add']
				];
				$purchase_batch->save();
			}
		}
				
		$new_invoice = new Invoice();
		$new_invoice->created_by = Auth::user()->id;
		$new_invoice->information = [
			'status' => 'active'
		];
		$new_invoice->save();


		for($counter = 0; $counter < count($purchase_id_list); $counter++){
			$new_invoice_purchase = new InvoicePurchase();
			$new_invoice_purchase->invoice_id = $new_invoice->id;
			$new_invoice_purchase->purchase_id = $purchase_id_list[$counter];
			$new_invoice_purchase->save();
		}

		$products = Product::getAllProducts();

		return response()->json([
			'action' => 'create-multiple-purchase-product',
			'request' => $request->all(),
			'product_results' => $product_results,
			'products' => $products
		]);
	}
}
