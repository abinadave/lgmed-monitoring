<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function insert(Request $request){
    	$this->validate($request, [
	        'program_name' => 'required|unique:programs|max:100|min:3',
	        'program_manager' => 'numeric|min:1',
	    ]);
	    echo "Good";
    }
}
