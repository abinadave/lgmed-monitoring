<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Program as Program;
use App\ProgramStat as ProgramStat; 

class ProgramController extends Controller
{
    public function delete($id){
        $count = Program::where('id', $id)->count();
        if ($count) {
            $program = Program::findOrFail($id);
            $rsProgram = $program->delete();
            if ($rsProgram) {
                return response()->json([
                    'rsProgram' => $rsProgram,
                    'rsProgramStat' => ProgramStat::where('program_id', $id)->delete()
                ]);
            }
        }
    }
    public function sortBy($attr, $type){
        $data = Program::orderBy($attr, $type)->get();
        return response()->json($data);
    }
    public function insert(Request $request){
    	$this->validate($request, [
	        'program_name' => 'required|max:100|min:3',
	        'program_manager' => 'numeric|min:1',
	    ]);
	    return $this->saveProgram($request);
    }
    private function saveProgram($request){
    	$program = new Program;
    	$program->program_name = $request->input('program_name');
    	$program->program_manager = $request->input('program_manager');
    	$program->save();
    	return response()->json($program);
    }
    public function fetch(){
        $data = array();
        if (Auth::user()->usertype == 'program-manager') {
            $data = Program::orderBy('id','desc')->where('program_manager', Auth::user()->id)->get();
        }else {
            $data = Program::orderBy('id','desc')->get();
        }
        return response()->json($data);
    	
    }
}
