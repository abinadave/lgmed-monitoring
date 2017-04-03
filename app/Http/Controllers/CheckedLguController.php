<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CheckedLgu as CheckedLgu;

class CheckedLguController extends Controller
{
    public function findAndinsert(Request $request){
    	$program_id = $request->input('program_id');
    	$province_id = $request->input('province_id');
    	$municipality_id = $request->input('municipality_id');
    	$count = CheckedLgu::where('program_id', $program_id)->where('municipality_id', $municipality_id)->count();
    	$completed = [];
    	$deleted = false;
    	if ($count == 0) {
    		$completed = new CheckedLgu;
	    	$completed->program_id = $program_id;
	    	$completed->municipality_id = $municipality_id;
	    	$completed->province_id = $province_id;
	    	$completed->save();
    	}else {
    		$deleted = CheckedLgu::
    			  where('program_id', $program_id)
    			->where('municipality_id', $municipality_id)->delete();
    	}
    	return response()->json([
    		'existed' => ($count > 0) ? true : false,
    		'checked_lgu' => $completed,
    		'deleted' => $deleted
    	]);
    	
    }

}
