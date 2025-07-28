<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;

class ProjectScheduleController extends Controller
{
    //
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $schedules = ProjectSchedule::where('project_id', $projectId)->paginate(10);

        return view('project-controller.project-schedules.index', compact('project', 'schedules'));
    }

    public function create($projectId)
    {
        $project = Project::findOrFail($projectId);
        return view('project-controller.project-schedules.form', ['project' => $project, 'schedule' => new ProjectSchedule()]);
    }

    public function store(Request $request, $projectId)
    {
        $request->validate(['name' => 'required|string|max:255']);

        ProjectSchedule::create([
            'project_id' => $projectId,
            'name' => $request->name,
        ]);

        return redirect()->route('projects.schedules.index', $projectId)
            ->with('success', 'Project schedule created successfully.');
    }

    public function edit($projectId, ProjectSchedule $schedule)
    {
        $project = Project::findOrFail($projectId);
        return view('project-controller.project-schedules.form', compact('project', 'schedule'));
    }

    public function update(Request $request, $projectId, ProjectSchedule $schedule)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $schedule->update(['name' => $request->name]);

        return redirect()->route('projects.schedules.index', $projectId)
            ->with('success', 'Project schedule updated successfully.');
    }

    public function destroy($projectId, ProjectSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('projects.schedules.index', $projectId)
            ->with('success', 'Project schedule deleted.');
    }
}
