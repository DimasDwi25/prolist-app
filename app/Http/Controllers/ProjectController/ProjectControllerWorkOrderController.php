<?php

namespace App\Http\Controllers\ProjectController;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Log;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrder;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;

class ProjectControllerWorkOrderController extends Controller
{
    //
    public function index()
    {
        $workOrders = WorkOrder::with([
            'project',
            'pic1User',
            'pic2User',
            'pic3User',
            'pic4User',
            'pic5User',
            'rolePic1',
            'rolePic2',
            'rolePic3',
            'rolePic4',
            'rolePic5'
        ])->get();

        // $wo = WorkOrder::with(['project.quotation.client', 'pic1User', 'pic2User'])->first();
        // dd($wo->toArray());


        return view('project-controller.work-order.index', compact('workOrders'));
    }

    // WorkOrderController.php

    public function create()
    {
        $projects = Project::with('quotation.client')->get();
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['engineer', 'electrician']);
        })->get();

        $roles = Role::where('type_role', 2)
                ->whereIn('name', ['Electrician', 'Engineer'])
                ->get();
        $projectWorkOrderCounts = WorkOrder::select('project_id', FacadesDB::raw('count(*) as total'))
            ->groupBy('project_id')->pluck('total', 'project_id');
        $categorieLogs = \App\Models\CategorieLog::all();
        $clients = Client::all();

        return view('project-controller.work-order.form', [
            'projects' => $projects,
            'users' => $users,
            'roles' => $roles,
            'workOrder' => $workOrder ?? null,
            'projectWorkOrderCounts' => $projectWorkOrderCounts,
            'engineerRoleId' => $roles->firstWhere('name', 'Engineer')?->id,
            'electricianRoleId' => $roles->firstWhere('name', 'Electrician')?->id,
            'categorieLogs' => $categorieLogs,
            'clients' => $clients,
        ]);

    }

    // Ambil nama client untuk project (AJAX)
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
            'project_id' => 'required|exists:projects,pn_number',
            'wo_date' => 'required|date',
            'wo_kode_no' => 'required|string',
            'wo_number_last' => 'required|integer',
            'pic1' => 'nullable|exists:users,id',
            'pic2' => 'nullable|exists:users,id',
            'pic3' => 'nullable|exists:users,id',
            'pic4' => 'nullable|exists:users,id',
            'pic5' => 'nullable|exists:users,id',
            'role_pic_1' => 'nullable|exists:roles,id',
            'role_pic_2' => 'nullable|exists:roles,id',
            'role_pic_3' => 'nullable|exists:roles,id',
            'role_pic_4' => 'nullable|exists:roles,id',
            'role_pic_5' => 'nullable|exists:roles,id',
            'total_mandays_eng' => 'required|integer',
            'total_mandays_elect' => 'required|integer',
            'work_description' => 'nullable|string',
        ]);

        // Simpan nomor urut
        $validated['wo_number_in_project'] = $validated['wo_number_last'];
        unset($validated['wo_number_last']);

        $wo = WorkOrder::create($validated);

        // Simpan log kalau user pilih "Ya"
        if ($request->save_log === 'yes') {
            $request->validate([
                'categorie_log_id' => 'required|exists:categorie_logs,id',
                'status' => 'required|in:open,close',
            ]);

            Log::create([
                'categorie_log_id' => $request->categorie_log_id,
                'users_id' => auth()->id(),
                'logs' => $validated['work_description'] ?? '-',
                'tgl_logs' => now(),
                'status' => $request->status,
                'closing_date' => $request->status === 'close' ? now() : null,
                'closing_users' => $request->status === 'close' ? auth()->id() : null,
                'response_by' => null,
                'need_response' => $request->boolean('need_response', false),
                'project_id' => $validated['project_id'],
            ]);
        }

        return redirect()->route('engineer.work_order')
            ->with('success', 'Work Order created successfully.');
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
            'project_id' => 'required|exists:projects,pn_number',
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

        return redirect()->route('engineer.work_order')->with('success', 'Work Order updated successfully.');
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

        return redirect()->route('engineer.work-order')->with('success', 'Work description inserted into project log.');
    }
}
