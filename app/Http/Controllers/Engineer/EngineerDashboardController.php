<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EngineerDashboardController extends Controller
{
    //
    public function index()
    {
        return view('engineer.dashboard');
    }
}
