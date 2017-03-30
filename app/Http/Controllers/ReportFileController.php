<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReportFile as ReportFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportFileController extends Controller
{
    public function fetchByStatId($id){
    	$data = ReportFile::where('program_stat_id', $id)->get();
    	return response()->json($data);
    }
    public function upload(Request $request){
    	if ($request->hasFile('photo')) {
    		$program_stat_id = $request->input('program_stat_id');
    		$program_id = $request->input('program_id');
		    $file = $request->file('photo');
		    $filename = $file->getClientOriginalName();
		    $directory = 'report-files/' . $program_id . '-' . $program_stat_id;
		    $dirs = Storage::directories('report-files');
		    if (!in_array($directory, $dirs)) {
                Storage::makeDirectory($directory);
			}
            return $this->uploadtoDrive($directory, $filename, $file, $request);
		}else {
            return response()->json([
                'file_found' => false
            ]);
        }
    }
    public function downloadFile(Request $request){
        // $dir = 'report-files/' . $request->input('program_id') . '-' . $request->input('program_id')
        // $file =Storage::get($dir);
        // return response()->download($pathToFile);
        $source = $request->input('source');
        $program_id = $request->input('program_id');
        $program_stat_id = $request->input('program_stat_id');
        echo "source: $source . <br>";
        echo "program_id: $program_id . <br>";
        echo "program_stat_id: $program_stat_id . <br>";
    }
    public function uploadtoDrive($directory, $filename, $file, $request){
    	$resp = Storage::put($directory . '/' .$filename, file_get_contents($file));
        $reportFile = new ReportFile;
        $reportFile->source = $filename;
        $reportFile->program_stat_id = $request->input('program_stat_id');
        $reportFile->uploaded_by = Auth::user()->id;
        $reportFile->save();
        return response()->json([
            'uploaded' => $resp,
            'report_file' => $reportFile,
            'file_found' => true
        ]);
    }
}
