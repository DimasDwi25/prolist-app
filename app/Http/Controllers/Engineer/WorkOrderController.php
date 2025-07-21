<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Log;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrder;
use DB;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    //
    public function index()
    {
        $workOrders = WorkOrder::with('project', 'client', 'pic1', 'pic2', 'pic3', 'pic4', 'pic5', 'role_pic_1', 'role_pic_2', 'role_pic_3', 'role_pic_4', 'role_pic_5')->get();
        return view('engineer.work_order.index', compact('workOrders'));
    }

    // WorkOrderController.php

    public function create()
    {
        $projects = Project::with('quotation.client')->get();
        $users = User::all();
        $roles = Role::where('type_role', 2)->get();
        $projectWorkOrderCounts = WorkOrder::select('project_id', DB::raw('count(*) as total'))
            ->groupBy('project_id')->pluck('total', 'project_id');

        return view('engineer.work_order.form', [
            'projects' => $projects,
            'users' => $users,
            'roles' => $roles,
            'workOrder' => $workOrder ?? null,
            'projectWorkOrderCounts' => $projectWorkOrderCounts,
            'engineerRoleId' => $roles->firstWhere('name', 'Engineer')?->id,
            'electricianRoleId' => $roles->firstWhere('name', 'Electrician')?->id,
        ]);

    }


    public function getClient(Project $project)
    {
        return response()->json([
            'client_id' => $project->quotation->client->id ?? null,
            'client_name' => $project->quotation->client->name ?? '-'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wo_date' => 'required|date',
            'wo_number_in_project' => 'required|integer',
            'wo_kode_no' => 'required|string',
            'total_mandays_eng' => 'required|integer',
            'total_mandays_elect' => 'required|integer',
            'work_description' => 'nullable|string',
            'add_to_log' => 'nullable|boolean',
        ]);

        $wo = WorkOrder::create($validated);
        if (WorkOrder::where('project_id', $request->project_id)->where('wo_number_in_project', $request->wo_number_in_project)->exists()) {
            return back()->withErrors(['wo_number_in_project' => 'WO number in project sudah ada untuk project ini.'])->withInput();
        }


        if ($request->add_to_log && $request->filled('work_description')) {
            Log::create([
                'project_id' => $request->project_id,
                'user_id' => auth()->id(),
                'kategori_log_id' => 1, // sesuaikan
                'isi' => $request->work_description,
                'tgl_logs' => now(),
            ]);
        }

        return redirect()->route('work-orders.index')->with('success', 'Work Order created successfully.');
    }

    public function edit(WorkOrder $workOrder)
    {
        $projects = Project::all();
        $clients = Client::all();
        $users = User::all();
        $roles = Role::where('type_role', 2)->get();
        return view('work_orders.edit', compact('workOrder', 'projects', 'clients', 'users', 'roles'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wo_date' => 'required|date',
            'wo_number_in_project' => 'required|integer',
            'wo_kode_no' => 'required|string|max:255',
            'total_mandays_eng' => 'required|integer',
            'total_mandays_elect' => 'required|integer',
            'work_description' => 'nullable|string',
        ]);

        $workOrder->update($request->only([
            'project_id',
            'wo_date',
            'wo_number_in_project',
            'wo_kode_no',
            'pic1',
            'pic2',
            'pic3',
            'pic4',
            'pic5',
            'role_pic_1',
            'role_pic_2',
            'role_pic_3',
            'role_pic_4',
            'role_pic_5',
            'total_mandays_eng',
            'total_mandays_elect',
            'work_description'
        ]));

        return redirect()->route('work-orders.index')->with('success', 'Work Order updated successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['project', 'client', 'pic1User', 'pic2User', 'pic3User', 'pic4User', 'pic5User', 'rolePic1', 'rolePic2', 'rolePic3', 'rolePic4', 'rolePic5']);
        return view('work_orders.show', compact('workOrder'));
    }

    public function insertLogFromWO(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        Log::create([
            'project_id' => $wo->project_id,
            'user_id' => auth()->id(),
            'category_log_id' => null, // jika pakai kategori
            'log' => $wo->work_description,
            'tgl_logs' => now(),
        ]);

        return redirect()->route('work-orders.index')->with('success', 'Work description inserted into project log.');
    }



}
