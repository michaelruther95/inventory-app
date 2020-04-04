<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\PurchaseBatch;
use App\Invoice;
use App\InvoicePurchase;
use App\ProductBatch;

class InvoicesController extends Controller
{
    public function index(Request $request){
    	$invoices = Invoice::with('invoicePurchases.purchaseInfo.itemInfo')->get();

    	return response()->json([
    		'invoices' => $invoices,
    	]);
    }

    public function delete(Request $request){
    	$invoice_info = Invoice::where('id', $request->invoice_id)->first();
    	if($invoice_info){
    		$invoice_purchases = InvoicePurchase::where('invoice_id', $invoice_info->id)->get();
    		if(count($invoice_purchases) > 0){
    			foreach($invoice_purchases as $invoice_purchase){
    				$purchase_info = Purchase::where('id', $invoice_purchase->purchase_id)->first();
    				$purchase_batches = PurchaseBatch::where('purchase_id', $purchase_info->id)->get();
    				if(count($purchase_batches) > 0){
    					foreach($purchase_batches as $purchase_batch){
    						$product_batch = ProductBatch::where('id', $purchase_batch->product_batch_id)->first();
    						$product_batch_information = $product_batch->information;
    						unset($product_batch_information['remaining_stocks']);

    						$product_batch_information['stocks_purchased'] = $product_batch_information['stocks_purchased'] - $purchase_batch['information']['stocks_purchased'];

    						$remaining_stocks = $product_batch_information['number_of_stocks'] - $product_batch_information['stocks_purchased'];
    						if($remaining_stocks <= 0){
    							$product_batch_information['is_still_available'] = false;
    						}
    						else{
    							$product_batch_information['is_still_available'] = true;
    						}

    						$product_batch->information = $product_batch_information;
    						$product_batch->save();
    					}
    				}

    				$purchase_info->status = 'void';
    				$purchase_info->save();
    			}
    		}

    		$invoice_information = $invoice_info->information;
    		$invoice_information['status'] = 'void';
    		$invoice_info->information = $invoice_information;
    		$invoice_info->save();
    	}

    	$invoice_info_final = Invoice::with('invoicePurchases.purchaseInfo.itemInfo')
    							->where('id', $request->invoice_id)
    							->first();

        $new_log = \App\Log::createLog([
            'action' => 'void_invoice',
            'invoice_id' => $request->invoice_id,
            'user_id' => auth()->user()->id,
            'message' => ':user voided an invoice with an ID of <strong>'.$request->invoice_id.'</strong>'
        ]);

    	return response()->json([
    		'action' => 'invoice',
    		'request' => $request->all(),
    		'invoice_info' => $invoice_info_final
    	]);
    }
}
