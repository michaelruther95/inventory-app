<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\PasswordReset;
use App\ConsultationFee;
use App\Http\Requests\AuthRequest;
use App\Helpers\RandomStringGeneratorHelper;
use App\Http\Requests\AccountSettingsRequest;
use App\Mail\ForgotPassword;
use DB;
use Auth;
use Mail;
use Validator;

class AuthController extends Controller
{
    public function index(Request $request){
        $user_info = User::with('userRole.role', 'consultationFee')
                ->where('id', Auth::user()->id)
                ->first();

    	return response()->json([
            'action' => 'auth-controller-index',
            'user_info' => $user_info
        ]);
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

                    $mail_params = [
                        'url' => env('APP_URL').'/reset-password/'.$randomString
                    ];

                    Mail::to($request->email_address)->send(new ForgotPassword($mail_params));
    			}
    		}

    		return response()->json([
    			'random_string' => $randomString
    		]);
    	}
    	// -------------------------------------------------------------
    }

    public function update(AccountSettingsRequest $request){
        // -------------------------------------------------------------
        // SAVE USER INFO
        // -------------------------------------------------------------
        $user = User::where('id', Auth::user()->id)->first();
        $user->profile = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
        ];
        $user->email = $request->email;
        if($request->new_password){
            $user->password = bcrypt($request->new_password);
        }
        $user->save();
        // -------------------------------------------------------------


        // -------------------------------------------------------------
        // SAVE CONSULTATION INFO
        // -------------------------------------------------------------
        $consultation_fee_info = ConsultationFee::where('doctor_id', Auth::user()->id)->first();
        if($consultation_fee_info){
            $consultation_fee_info->fee = $request->consultation_fee;
            $consultation_fee_info->save();
        }
        // -------------------------------------------------------------

        $user_info = User::with('userRole.role', 'consultationFee')
                ->where('id', Auth::user()->id)
                ->first();

        return response()->json([
            'user_info' => $user_info
        ]);
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

    public function reset(Request $request){
        $validator = Validator::make($request->all(), [
                        'email_address' => 'required|email|exists:password_resets,email',
                        'new_password' => 'required|min:6',
                        'confirm_new_password' => 'required|min:6|same:new_password',
                        'token' => 'required|exists:password_resets,token'
                    ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->messages()
            ], 422);
        }

        $check_record = PasswordReset::where('token', $request->token)
                        ->where('email', $request->email_address)
                        ->first();
        if(!$check_record){
            return response()->json([
                'error' => 'No password reset record found. Please try to request a new password reset in the forgot password page.'
            ], 403);
        }

        $delete_record = PasswordReset::where('token', $request->token)
                        ->where('email', $request->email_address)
                        ->delete();


        $user_info = User::where('email', $request->email_address)->first();
        $user_info->password = bcrypt($request->new_password);
        $user_info->save();

        return response()->json([
            'action' => 'reset-password',
        ]);
    }
}
