<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubmittedDateController extends Controller
{
    public function insert(Request $request){
    	$this->validate($request, [
	        'date' => 'required|date'
	    ]);
	    $date = $request->input('date');
	    $stat = $request->input('stat');
	    print_r($stat);
    }
}
