<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;

class InvoicesController extends Controller
{
    public function index(Request $request){
    	$purchases = Purchase::with('itemInfo')->get();
    	return response()->json([
    		'action' => 'get-invoices',
    		'purchases' => $purchases
    	]);
    }
}
