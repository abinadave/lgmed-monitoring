<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\CheckedLgu as CheckedLgu;

class CheckedLguController extends Controller
{
    public function checkOrUncheck(Request $request){
        $program_id      = $request->input('program_id');
        $program_stat_id = $request->input('program_stat_id');
        $province_id     = $request->input('province_id');
        $type     = $request->input('type');
        $deleted_count = $this->deleteFirst($request);
        $saved = array();
        $deleted = array();
        if ($type == 'check-all') {
            $saved = $this->checkAll($request);
        }else {
            $deleted = $this->checkAll();
        }
        return response()->json([
            'deleted_count' => $deleted_count,
            'deleted' => $deleted,
            'saved' => $saved
        ]);
    }
    private function checkAll($request){
        $cities = $request->input('cities');
        $saved = array();
        foreach ($cities as $key => $city) {
            $checked = new CheckedLgu;
            $checked->program_id = $request->input('program_id');
            $checked->program_stat_id = $request->input('program_stat_id');
            $checked->municipality_id = $city['id'];
            $checked->province_id = $request->input('province_id');
            $checked->user_id = Auth::user()->id;
            $checked->save();
            array_push($saved, $checked);
        }
        return $saved;
    }
    private function deleteFirst($request){
        $deleted = CheckedLgu::where('program_id', $request->input('program_id'))
                    ->where('program_stat_id', $request->input('program_stat_id'))
                    ->where('province_id', $request->input('province_id'))
                    ->delete();
        return $deleted;
    }
    public function fetchCheckedByProgram(Request $request){
        $program_id  = $request->input('program_id');
        $data = CheckedLgu::where('program_id', $program_id)->get();
        return response()->json($data);
    }
    public function fetchByHeaders(Request $request){
        $program_id      = $request->input('program_id');
        $program_stat_id = $request->input('program_stat_id');
        $province_id     = $request->input('province_id');
        $data = CheckedLgu::where('program_id', $program_id)
            ->where(function ($query) use ($program_stat_id, $province_id) {
                $query->where('program_stat_id', '=', $program_stat_id)
                      ->where('province_id', '=', $province_id);
            })
            ->get();
        return response()->json($data);
    }
    public function findAndinsert(Request $request){
    	$program_id      = $request->input('program_id');
        $program_stat_id = $request->input('program_stat_id');
    	$province_id     = $request->input('province_id');
    	$municipality_id = $request->input('municipality_id');
    	$count = CheckedLgu::where('program_id', $program_id)
                    ->where('municipality_id', $municipality_id)
                    ->where('program_stat_id', $program_stat_id)
                    ->count();
    	$completed = [];
    	$deleted = false;
    	if ($count == 0) {
    		$completed = new CheckedLgu;
	    	$completed->program_id = $program_id;
            $completed->program_stat_id = $program_stat_id;
	    	$completed->municipality_id = $municipality_id;
	    	$completed->province_id = $province_id;
            $completed->user_id = Auth::user()->id;
	    	$completed->save();
    	}else {
    		$deleted = CheckedLgu::where('program_id', $program_id)
                ->where('program_stat_id', $program_stat_id)
    			->where('municipality_id', $municipality_id)->delete();
    	}
    	return response()->json([
    		'existed' => ($count > 0) ? true : false,
    		'checked_lgu' => $completed,
    		'deleted' => $deleted
    	]);
    	
    }

}