<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Province as Province;
use App\Lgu as Lgu;
class ProvinceController extends Controller
{
    public function fetch(){
    	return response()->json([
    		'provinces' =>  Province::orderBy('name','asc')->get(),
    		'lgus' =>  Lgu::orderBy('name','asc')->get(),
    	]);
    }
}
