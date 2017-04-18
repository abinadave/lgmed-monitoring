<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\CheckedBrgy as CheckedBrgy;
use App\Brgy as Brgy;
class CheckedBrgyController extends Controller
{
    public function checkUncheckAll(Request $request){
        $program_stat_id = $request->input('program_stat_id');
        $municipality_id = $request->input('municipality_id');
        $type = $request->input('type');
        $deleted = null;
        $saved_models = array();
        $deleted_models = array();
        if ($type === 'checkall') {
            $deleted = CheckedBrgy::where('program_stat_id', $program_stat_id)
                                  ->where('municipality_id', $municipality_id)
                                  ->delete();
            $brgyIds = Brgy::where('municipality_id', $municipality_id)->pluck('id');       
            foreach ($brgyIds as $bid) {
                $checkedBrgy = new CheckedBrgy;
                $checkedBrgy->municipality_id = $municipality_id;
                $checkedBrgy->brgy_id = $bid;
                $checkedBrgy->program_stat_id = $request->input('program_stat_id');
                $checkedBrgy->user_id = Auth::user()->id;
                $checkedBrgy->save();
                array_push($saved_models, $checkedBrgy);
            }             
        }else {
            $deleted_models = CheckedBrgy::where('program_stat_id', $program_stat_id)
                                  ->where('municipality_id', $municipality_id)
                                  ->get();
            $deleted = CheckedBrgy::where('program_stat_id', $program_stat_id)
                                  ->where('municipality_id', $municipality_id)
                                  ->delete();
        }

        return response()->json([
            'type'           => $type,
            'saved_models'   => $saved_models,
            'delete_count'   => $deleted,
            'deleted_models' => $deleted_models
        ]);
    }

    public function fetchAll(){
        $data = CheckedBrgy::all();
        return response()->json($data);
    }

	public function fetchByLgu(Request $request){
		$program_stat_id = $request->input('program_stat_id');
		$municipality_id = $request->input('municipality_id');
		$data = CheckedBrgy::where('program_stat_id', $program_stat_id)
    						->where('municipality_id', $municipality_id)->get();
    	return response()->json($data);
	}

    public function insertOrDelete(Request $request){
    	$program_stat_id = $request->input('program_stat_id');
    	$municipality_id = $request->input('municipality_id');
    	$brgy_id = $request->input('brgy_id');
    	$action = $request->input('is_checked');

    	$saved = array();
    	$type = '';
    	$deleted = 0;
    	$deletedCheckedBrgy = null;

    	if ($action === 'delete') {
    		$deletedCheckedBrgy = CheckedBrgy::where('program_stat_id', $program_stat_id)
    						->where('municipality_id', $municipality_id)
    						->where('brgy_id', $brgy_id)->first();
    		$deleted = CheckedBrgy::where('program_stat_id', $program_stat_id)
    						->where('municipality_id', $municipality_id)
    						->where('brgy_id', $brgy_id)->delete();
    		$type = 'delete';
    	}else {
    		$checkedBrgy = new CheckedBrgy;
    		$checkedBrgy->program_stat_id = $program_stat_id;
    		$checkedBrgy->municipality_id = $municipality_id;
    		$checkedBrgy->brgy_id = $brgy_id;
    		$checkedBrgy->user_id = Auth::user()->id;
    		$checkedBrgy->save();
    		$saved = $checkedBrgy;
    		$type = 'save';
    	}
    	return response()->json([
    		'type'  => $type,
    		'saved' => $saved,
    		'deleted' => $deleted,
    		'deleted_model' => $deletedCheckedBrgy
    	]);
    }

}
