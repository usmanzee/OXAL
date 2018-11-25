<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Validator;
use Illuminate\Http\Request;

class UsersController extends Controller
{

	public function register(Request $request) {

	    $validator = Validator::make($request->all(), [
	        'name' => 'required',
	        'email' => 'required|email',
	        'password' => 'required',
	        'cnic_or_passport_number' => 'required'
	    ]);

	    if($validator->fails()) {
	        return [
	            'status' => false,
	            'errors' => $validator->errors(),
	            'message' => "Please provide valid information."
	        ];
	    }

	    if(!User::where('email', $request->email)->exists()) {

	        $input = $request->all();
	        $input['password'] = bcrypt($input['password']);
	        $user = User::create($input);
	        $output = [
	            'status' => true,
	            'data' => $user,
	            'message' => 'You are registered successfully.'
	        ];
	    } else {
	        $output = [
	            'status' => false,
	            'data' => 'An account already exists by this email address.'
	        ];
	    }

	    return response()->json($output);
	}

	public function login(Request $request) {
		$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Please provide valid information."
            ];
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $output = [
                'status' => true,
                'data' => $user
            ];
        } else {
            $output = [
                'status' => false,
                'message' => 'Invalid credentials'
            ];
        }

        return response()->json($output);
	}
}
