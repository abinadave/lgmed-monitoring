<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\User as User;

class UserController extends Controller
{
    public function insert(Request $request){
    	$this->validate($request, [
	        'name' => 'required',
	        'email' => 'required|email',
	        'password' => 'required|confirmed',
	        'usertype' => 'required'
	    ]);
	    return $this->saveUser($request);
    }
    private function saveUser($request){
    	$user = new User;
    	$user->name = $request->input('name');
    	$user->email = $request->input('email');
    	$user->password = Hash::make($request->input('password'));
    	$user->usertype = $request->input('usertype');
    	$user->save();
    	return response()->json($user);
    }
    public function fetch(){
        return response()->json([
            'users' => User::orderBy('id','desc')->get()
        ]);
    }
}
