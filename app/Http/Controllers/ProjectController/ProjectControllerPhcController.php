<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\PHC;
use Illuminate\Http\Request;

class ProjectControllerPhcController extends Controller
{
    //
    public function show(PHC $phc)
    {
        $phc->load('project.quotation');
        return view('project-controller.phcs.show', compact('phc'));
    }
}
