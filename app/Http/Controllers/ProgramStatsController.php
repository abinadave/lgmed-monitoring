<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramStatsController extends Controller
{
    public function programView($programId){
    	echo $programId;
    }
}
