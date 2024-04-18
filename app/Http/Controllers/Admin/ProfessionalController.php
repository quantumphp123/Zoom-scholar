<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interest;
use Mail;
use PDF;

class InterestController extends Controller
{

    public function Index()
    {
        $data=Interest::get();
         //dd($users);
       return view('Admin.Interest.Index', compact('data'));
    }


    
}
