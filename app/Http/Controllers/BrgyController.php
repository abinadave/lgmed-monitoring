<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brgy as Brgy;
use App\Lgu as Municipality;
use DB;
class BrgyController extends Controller
{
    public function fetch(){
        $data = Brgy::all();
        return response()->json($data);
    }
    public function updateBrgys(){
    	$brgys = Brgy::all();
    	$municipalities =  Municipality::all();
    	$name = '';
    	foreach ($brgys as $brgy) {
    		$found = $municipalities->contains('name', $brgy['municipality_name']);
    		if ($found) {
    			$city = $municipalities->where('name', $brgy['municipality_name'])->first();
    			$brgy->municipality_id = $city['id'];
    			$brgy->save();
    		}else {
    			echo "not<br>";
    		}
    	}
    }
}
