<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthController extends Controller
{
    public function index(Request $request){
    	return response()->json(['action' => 'auth-controller-index']);
    }

    public function create(Request $request){
    	$allowedActions = ['login', 'forgot_password'];
    	if(!in_array($request->action, $allowedActions)){
    		return response()->json(['error_message' => 'Action not allowed.'], 401);
    	}

    	// -------------------------------------------------------------
    	// LOGIN USER
    	// -------------------------------------------------------------
    	if($request->action == 'login'){
    		if(Auth::attempt(['email' => $request->email_address, 'password' => $request->password])){
	            $user = User::with('userRole.role')->select("*")->where('email', '=' ,$request->email_address)->first();
	    		
	    		return response()->json([
	    			'token' => $user->createToken('Tokenizer')->accessToken,
	    			'user_info' => $user
	    		]);
	        }
	        else{
	            return response()->json([
	            	'error_message' => 'The provided credentials are invalid.'
	            ], 403);
	        }
    	}
    	// -------------------------------------------------------------


    	// -------------------------------------------------------------
    	// FORGOT PASSWORD
    	// -------------------------------------------------------------
    	if($request->action == 'forgot-password'){

    	}
    	// -------------------------------------------------------------
    }

    public function update(Request $request){

    }

    public function delete(Request $request){
    	// -------------------------------------------------------------
    	// LOGOUT USER
    	// -------------------------------------------------------------
    	$request->user()->token()->revoke();

    	return response()->json([
			'action' => 'logout'
		]);
		// -------------------------------------------------------------
    }
}
