<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;

class UsersController extends Controller
{
    public function show(Request $request){
    	$roleInfo = Role::where('name', $request->type)->first();
    	if(!$roleInfo){
    		return response()->json([
    			'error_message' => 'Invalid Role!'
    		], 403);
    	}

    	$users = User::with('userRole')->whereHas('userRole', function($query) use ($roleInfo) {
    		$query->where('role_id', $roleInfo->id);
    	})->get();

    	return response()->json([
    		'type' => $request->type,
    		'users' => $users
    	]);
    }

    public function update(){
        
    }
}
