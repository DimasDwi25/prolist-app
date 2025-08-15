<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\ProjectScheduleTask;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectScheduleTaskController extends Controller
{
    //
    public function index(Project $project, ProjectSchedule $schedule)
    {
        $tasks = ProjectScheduleTask::with('task')
            ->where('project_schedule_id', $schedule->id)
            ->orderBy('order')
            ->paginate(10);

        return view('project-controller.schedule-tasks.index', compact('project', 'schedule', 'tasks'));
    }

    public function create(Project $project, ProjectSchedule $schedule)
    {
        $taskMasters = Task::all(); // Ambil task dari master
        return view('project-controller.schedule-tasks.form', [
            'project' => $project,
            'schedule' => $schedule,
            'task' => new ProjectScheduleTask(),
            'taskMasters' => $taskMasters
        ]);
    }

    public function store(Request $request, Project $project, ProjectSchedule $schedule)
    {
        $data = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'bobot' => 'required|numeric|min:0|max:100',
            'plan_start' => 'required|date',
            'plan_finish' => 'required|date|after_or_equal:plan_start',
            'actual_start' => 'nullable|date',
            'actual_finish' => 'nullable|date|after_or_equal:actual_start',
            'order' => 'nullable|integer|min:0'
        ]);

        $data['project_schedule_id'] = $schedule->id;
        ProjectScheduleTask::create($data);

        return redirect()
            ->route('projects.schedule-tasks.index', [$project->pn_number, $schedule->id])
            ->with('success', 'Task added successfully!');
    }

    public function edit(Project $project, ProjectSchedule $schedule, ProjectScheduleTask $task)
    {
        $taskMasters = Task::all();
        return view('project-controller.schedule-tasks.form', compact('project', 'schedule', 'task', 'taskMasters'));
    }

    public function update(Request $request, Project $project, ProjectSchedule $schedule, ProjectScheduleTask $task)
    {
        $data = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'bobot' => 'required|numeric|min:0|max:100',
            'plan_start' => 'required|date',
            'plan_finish' => 'required|date|after_or_equal:plan_start',
            'actual_start' => 'nullable|date',
            'actual_finish' => 'nullable|date|after_or_equal:actual_start',
            'order' => 'nullable|integer|min:0'
        ]);

        $task->update($data);

        return redirect()
            ->route('projects.schedule-tasks.index', [$project->pn_number, $schedule->id])
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(Project $project, ProjectSchedule $schedule, ProjectScheduleTask $task)
    {
        $task->delete();
        return redirect()
            ->route('projects.schedule-tasks.index', [$project->pn_number, $schedule->id])
            ->with('success', 'Task deleted successfully!');
    }
}
