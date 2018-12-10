<?php

namespace App\Http\Controllers;

use File;
use Auth;
use Session;
use Redirect;
use App\User;
use Validator;
use App\Helper;
use Illuminate\Http\Request;

class UsersController extends Controller
{

	public function index() {
		$users = User::all();
		return view('users/index', compact('users'));
	}

	public function add() {
		return view('users/add');
	}

	public function store(Request $request) {
		$this->validate($request,[
            'name' => 'required',
	        'email' => 'required|email|unique:users',
	        'password' => 'required',
	        'phone_number' => 'required',
	        'cnic_or_passport_number' => 'required'
        ]);

		$input = $request->all();
		$input['password'] = bcrypt($input['password']);
        $allowedfileExtension = ['png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp'];
	    if($request->hasFile('file')) {
	    	$file = $request->file('file');
    		$extension = $file->getClientOriginalExtension();
    		$uploadNameWithoutExt = date('Ymd-His');
    		$uploadName = date('Ymd-His').'.'.$extension;

    		if(in_array($extension, $allowedfileExtension)) {

    			$path = public_path('user_avatars');
				if(!File::exists($path)) {
					File::makeDirectory($path, $mode = 0777, true, true);
				}
    			$file->move($path, $uploadName);

		        $input['avatar_name'] = $uploadName;
		        $input['avatar_name_without_ext'] = $uploadNameWithoutExt;
		        $input['avatar_ext'] = $extension;
    		}
	    }

        $user = User::create($input);

        Session::put('success', 'User created successfully.');
        return redirect('admin/users');
	}

	public function edit($id) {
		$user = User::where('id', $id)->first();
		return view('users/edit', compact('user'));
	}

	public function update($id, Request $request) {

		$this->validate($request,[
            'name' => 'required',
	        'email' => 'required|email',
	        'phone_number' => 'required',
	        'cnic_or_passport_number' => 'required'
        ]);

        $params = [
        	'name' => $request->name,
			'email' => $request->email,
			'phone_number' => $request->phone_number,
			'cnic_or_passport_number' => $request->cnic_or_passport_number,
        ];

        $allowedfileExtension = ['png', 'jpg', 'jpeg', 'gif', 'tif', 'bmp', 'ico', 'psd', 'webp'];
	    if($request->hasFile('file')) {
	    	$file = $request->file('file');
    		$extension = $file->getClientOriginalExtension();
    		$uploadNameWithoutExt = date('Ymd-His');
    		$uploadName = date('Ymd-His').'.'.$extension;

    		if(in_array($extension, $allowedfileExtension)) {

    			$path = public_path('user_avatars');
				if(!File::exists($path)) {
					File::makeDirectory($path, $mode = 0777, true, true);
				}
    			$file->move($path, $uploadName);

		        $params['avatar_name'] = $uploadName;
		        $params['avatar_name_without_ext'] = $uploadNameWithoutExt;
		        $params['avatar_ext'] = $extension;
    		}
	    }
        User::where('id', $id)->update($params);

        Session::put('success', 'User updated successfully.');
        return redirect('admin/users');
	}

	public function delete($id, Request $request) {
		User::where('id', $id)->delete();
		Session::put('success', 'User deleted successfully.');
        return redirect('admin/users');
	}

	public function register(Request $request) {

	    $validator = Validator::make($request->all(), [
	        'name' => 'required',
	        'email' => 'required|email|unique:users',
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

	public function getUserDetail(Request $request) {

		$userId = $request->userId;
		$user = (new User)->getUserById($userId);

		if(!is_null($user)) {
			$output = [
				'status' => true,
				'data' => $user
			];
		} else {
			$output = [
				'status' => false,
				'message' => 'User does not exists.'
			];
		}

		return response()->json($output);
	}

	public function updateUserPhoneNumberAndSendVerificationCode(Request $request) {

		$userId = $request->userId;
		$phoneNumber = $request->phoneNumber;

		$user = (new User)->getUserByPhoneNumber($phoneNumber);

		if(is_null($user) || (!is_null($user) && $userId == $user->id)) {
			
			$verificationCode = Helper::createRandomNumber(4);
			User::where('id', $userId)->update([
				'phone_number' => $phoneNumber,
				'verification_code' => $verificationCode
			]);
			$output = [
				'status' => true,
				'data' => 'verification code sent successfully.'
			];
			} else {
				$output = [
                    'status' => false,
                    'message' => 'Another user already exists with this number.'
                ];
			}
		return response()->json($output);
	}
}
