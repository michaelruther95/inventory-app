<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\UserRole;
use App\ConsultationFee;
use App\Http\Requests\UsersRequest;
use Auth;

use App\Mail\NewUser as NewUserMail;

use Mail;

class UsersManagementController extends Controller
{
    public function show(Request $request){
    	$users = User::with('userRole.role')
    			->where('email', '!=', Auth::user()->email)
    			->get();

    	return response()->json([
    		'action' => 'get-all-users',
    		'users' => $users
    	]);
    }

    public function create(UsersRequest $request){
    	$user = new User();
    	$user->profile = [
    		'first_name' => $request->first_name,
    		'last_name' => $request->last_name,
    		'gender' => $request->gender
    	];
    	$user->email = $request->email;

    	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $temp_password = '';
        $length_limit = 10;
        for ($counter = 0; $counter <= $length_limit; $counter++) {
            $temp_password .= $characters[rand(0, $characters_length - 1)];
        }
    	$user->password = bcrypt($temp_password);
    	$user->save();

    	$user_role = new UserRole();
    	$user_role->role_id = $request->account_type;
    	$user_role->user_id = $user->id;
    	$user_role->save();

    	if($request->account_type == 1){
    		$consultation_fee = new ConsultationFee();
    		$consultation_fee->doctor_id = $user->id;
    		$consultation_fee->fee = env('DEFAULT_CONSULTATION_FEE');
    		$consultation_fee->save();
    	}

    	$mail_params = [
    		'first_name' => $request->first_name,
    		'email' => $request->email,
    		'password' => $temp_password,
    		'url' => env('APP_URL')
    	];
    	Mail::to($request->email)->send(new NewUserMail($mail_params));

    	$user_info = User::with('userRole.role')->where('id', $user->id)
    			->first();

    	return response()->json([
    		'user_info' => $user_info
    	]);
    }

    public function update(Request $request){
    	$is_disabled = false;
    	if($request->status == 'disable'){
    		$is_disabled = true;
    	}

    	$user_info = User::where('id', $request->id)->first();
    	$user_info->is_disabled = $is_disabled;
    	$user_info->save();

    	return response()->json([
    		'request' => $request->all()
    	]);
    }
}
