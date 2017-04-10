<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brgy as Brgy;
use App\Lgu as Municipality;
use DB;
class BrgyController extends Controller
{
    public function updateBrgys(){
    	$brgys = Brgy::all();
    	$municipalities =  Municipality::all();
    	$name = '';
    	foreach ($brgys as $brgy) {
    		$found = $municipalities->contains('name', $brgy['municipality_name']);
    		if ($found) {
    			$city = $municipalities->where('name', $brgy['municipality_name'])->first();
    			// echo $city['id'] . "<br>";
    			$brgy->municipality_id = $city['id'];
    			$brgy->save();
    			// echo rsUpdated;
    		}else {
    			echo "not<br>";
    		}
    	}
    }
}
