<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiseaseFinding;
use App\Purchase;

class DashboardController extends Controller
{
    public function index(){
    	return response()->json([
    		'disease_findings' => DiseaseFinding::with('disease')->get(),
    		'purchases' => Purchase::with('itemInfo')->get()
    	]);
    }
}
