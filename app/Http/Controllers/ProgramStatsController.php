<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProgramStat as ProgramStat;
use App\Program as Program;
class ProgramStatsController extends Controller
{
    public function programView($programId){
    	$reports = ProgramStat::where('program_id', $programId)->get();
    	return response()->json([
    		'reports' => $reports,
    		'program' => Program::findOrFail($programId)
    	]);
    }
    public function insert(Request $request){
    	$stat = new ProgramStat;
    	$stat->program_id = $request->input('program_id');
    	$stat->reporting_freq = $request->input('reporting_freq');
    	$stat->submission_date = $request->input('submission_date');
    	$stat->save();
    	return response()->json($stat);
    }
    public function fetchAll(){
        $stats = ProgramStat::all();
        return response()->json($stats);
    }
}
