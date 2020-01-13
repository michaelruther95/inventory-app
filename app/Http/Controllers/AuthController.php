<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
use App\Http\Requests\AuthRequest;
use App\Helpers\RandomStringGeneratorHelper;
use DB;
use Auth;

class AuthController extends Controller
{
    public function index(Request $request){
    	return response()->json(['action' => 'auth-controller-index']);
    }

    public function create(AuthRequest $request){
    	// -------------------------------------------------------------
    	// LOGIN USER
    	// -------------------------------------------------------------
    	if($request->action == 'login'){
    		if(!Auth::attempt(['email' => $request->email_address, 'password' => $request->password])){
	    		return response()->json([
	            	'error_message' => 'The provided credentials are invalid.'
	            ], 403);
	        }

	        $user = User::with('userRole.role')->select("*")->where('email', '=' ,$request->email_address)->first();
	    	
	        $deleteResetRecords = PasswordReset::where('email', $request->email_address)->delete();

    		return response()->json([
    			'token' => $user->createToken('Tokenizer')->accessToken,
    			'user_info' => $user
    		]);
    	}
    	// -------------------------------------------------------------


    	// -------------------------------------------------------------
    	// FORGOT PASSWORD
    	// -------------------------------------------------------------
    	if($request->action == 'forgot-password'){
    		$tokenExists = false;
    		while(!$tokenExists){
    			$randomString = RandomStringGeneratorHelper::generateRandomString(30);
    			$checkToken = PasswordReset::where('token', $randomString)->first();
    			if(!$checkToken){
    				$deleteCurrentRecord = PasswordReset::where('email', $request->email_address)->delete();

    				$tokenExists = true;
    				$resetRecord = new PasswordReset();
    				$resetRecord->email = $request->email_address;
    				$resetRecord->token = $randomString;
    				$resetRecord->created_at = now();
    				$resetRecord->save();
    			}
    		}

    		return response()->json([
    			'random_string' => $randomString
    		]);
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
