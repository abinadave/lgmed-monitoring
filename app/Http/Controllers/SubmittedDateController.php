<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\SubmittedDate as SubmittedDate;

class SubmittedDateController extends Controller
{
	public function fetch(){
		$data = SubmittedDate::all();
		return response()->json($data);
	}
    public function insert(Request $request){
    	$this->validate($request, [
	        'date' => 'required|date'
	    ]);
	    $date = $request->input('date');
	    $stat = $request->input('stat');
	    $model = new SubmittedDate;
	    $model->date_submitted = $date;
	    $model->program_stat_id = $stat['id'];
	    $model->person = Auth::user()->id;
	    $model->save();
	    return response()->json($model);
    }
}
