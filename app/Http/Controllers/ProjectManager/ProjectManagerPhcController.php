<?php

namespace App\Http\Controllers\ProjectManager;

use App\Http\Controllers\Controller;
use App\Models\PHC;
use Illuminate\Http\Request;

class ProjectManagerPhcController extends Controller
{
    //
    public function show(PHC $phc)
    {
        $phc->load('project.quotation');
        return view('project-manager.phcs.show', compact('phc'));
    }
}
