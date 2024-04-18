<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getProfile()
    {
        $data = auth()->user();
        return view('admin.profile.index', compact('data'));
    }
}
