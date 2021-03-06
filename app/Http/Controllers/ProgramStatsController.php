<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProgramStat as ProgramStat;
use App\Program as Program;
use App\ReportFile as ReportFile;
use Storage;
use App\CheckedLgu as CheckedLgu;

class ProgramStatsController extends Controller
{
    public function delete($id){
        $stat = ProgramStat::findOrFail($id);
        $program = Program::where('id', $stat->program_id)->first();
        $rs = $stat->delete();
        if ($rs) {
            $count = ReportFile::where('program_stat_id', $id)->count();
            $deletedCheckedLgu = CheckedLgu::where('program_stat_id', $id)->delete();
            if ($count > 0) {
                ReportFile::where('program_stat_id', $id)->delete();
            }
            $dir = 'report-files/'.$program->id . '-' . $id;
            $dirDeleted = Storage::deleteDirectory($dir);
            return response()->json([
                'deleted' => $rs,
                'file_count' => $count,
                'dir_deleted' => $dirDeleted,
                'directory' => $dir,
                'checked_lgu_delete' => $deletedCheckedLgu
            ]);
        }
    }
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
    	// $stat->submission_date = $request->input('submission_date');
    	$stat->save();
    	return response()->json($stat);
    }
    public function fetchAll(){
        $stats = ProgramStat::all();
        return response()->json($stats);
    }
}
