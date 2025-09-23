<?php

namespace App\Http\Controllers\API\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkOrderApiController extends Controller
{
    //
    /**
     * GET Work Orders by Project PN Number
     */
    public function index($pn_number)
    {
        $project = Project::where('pn_number', $pn_number)->firstOrFail();

        $workOrders = WorkOrder::with(['pics.user', 'pics.role', 'descriptions'])
            ->where('project_id', $project->pn_number)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workOrders,
        ]);

    }

    public function nextCode(Request $request)
    {
        $projectId = $request->get('project_id');
        $year = now()->format('y'); // contoh: "25" untuk 2025

        // Cari nomor terakhir WO di project ini
        $lastWo = WorkOrder::where('project_id', $projectId)
            ->whereYear('created_at', now()->year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastWo) {
            // asumsikan kode WO format "WO-001/25"
            $parts = explode('/', $lastWo->wo_kode_no);
            if (!empty($parts[0])) {
                $num = (int) filter_var($parts[0], FILTER_SANITIZE_NUMBER_INT);
                $nextNumber = $num + 1;
            }
        }

        $projectCode = substr($projectId, -3);

        $code = sprintf(
            "WO/%s/%s/%03d",
            $year,
            $projectCode,
            $nextNumber
        );


        return response()->json([
            'success' => true,
            'code' => $code,
        ]);
    }

    /**
     * CREATE new Work Order
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,pn_number',
            'wo_date' => 'required|date',
            'wo_count' => 'nullable|integer|min:1', // tambahan field untuk jumlah WO
            'add_work' => 'boolean',
            'pics' => 'array',
            'pics.*.user_id' => 'required|exists:users,id',
            'pics.*.role_id' => 'nullable|exists:roles,id',
            'descriptions' => 'array',
            'descriptions.*.description' => 'nullable|string',
            'descriptions.*.result' => 'nullable|string',
            'start_working_date' => 'nullable|date',
            'end_working_date' => 'nullable|date',
            'client_approved' => 'nullable|boolean',
        ]);

        $woCount = $data['wo_count'] ?? 1; // default 1 kalau tidak diisi
        $year = now()->format('y');

        // cari nomor terakhir di project yang sama & tahun berjalan
        $lastWo = WorkOrder::where('project_id', $request->project_id)
            ->whereYear('created_at', now()->year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastWo) {
            $parts = explode('/', $lastWo->wo_kode_no);
            if (!empty($parts[0])) {
                $num = (int) filter_var($parts[0], FILTER_SANITIZE_NUMBER_INT);
                $nextNumber = $num + 1;
            }
        }

        $workOrders = DB::transaction(function () use ($data, $nextNumber, $year, $woCount, $request) {
        $results = [];
        $baseDate = \Carbon\Carbon::parse($data['wo_date']);

        for ($i = 0; $i < $woCount; $i++) {
            $woData = $data;
            $woData['wo_date'] = $baseDate->copy()->addDays($i); 
            $woData['status'] = 'waiting approval';

            $lastInProject = WorkOrder::where('project_id', $data['project_id'])
                ->max('wo_number_in_project');
            $woNumberInProject = ($lastInProject ?? 0) + 1;

            $woData['wo_number_in_project'] = $woNumberInProject;
            $projectCode = substr($data['project_id'], -3);
            $woData['wo_kode_no'] = sprintf(
                "WO/%s/%s/%03d",
                $year,
                $projectCode,
                $woNumberInProject
            );

            $wo = WorkOrder::create([
                'project_id'           => $woData['project_id'],
                'wo_date'              => $woData['wo_date'],
                'wo_number_in_project' => $woData['wo_number_in_project'],
                'wo_kode_no'           => $woData['wo_kode_no'],
                'total_mandays_eng'    => 0, // sementara nanti dihitung
                'total_mandays_elect'  => 0,
                'add_work'             => $woData['add_work'] ?? false,
                'start_working_date'   => $woData['start_working_date'] ?? null,
                'end_working_date'     => $woData['end_working_date'] ?? null,
                'wo_count'             => $woData['wo_count'] ?? 1,
                'client_approved'      => $woData['client_approved'] ?? false,
                'created_by'           => $request->user()->id,
                'status'               => $woData['status'],
            ]);

            // PIC
            if (!empty($data['pics'])) {
                $wo->pics()->createMany($data['pics']);
            }

            // Descriptions
            if (!empty($data['descriptions'])) {
                $wo->descriptions()->createMany($data['descriptions']);
            }

            // Hitung mandays
            $totalEng = $wo->pics()->whereHas('role', fn($q) => $q->where('name', 'engineer'))->count();
            $totalElect = $wo->pics()->whereHas('role', fn($q) => $q->where('name', 'electrician'))->count();

            $wo->update([
                'total_mandays_eng'   => $totalEng,
                'total_mandays_elect' => $totalElect,
            ]);

            // Buat approval untuk Project Manager & Engineering Director
            $approvalRoles = ['project manager', 'engineering_director'];
            $users = User::whereHas('role', fn($q) => $q->whereIn('name', $approvalRoles))->get();

            foreach ($users as $user) {
                Approval::create([
                    'approvable_type' => WorkOrder::class,
                    'approvable_id'   => $wo->id,
                    'user_id'         => $user->id,
                    'status'          => 'pending',
                    'type'            => 'Work Order',
                ]);
            }

            $results[] = $wo->load(['pics.user', 'pics.role', 'descriptions']);
        }

        return $results;
    });


        return response()->json([
            'status' => 'success',
            'data'   => $workOrders,
        ], 201);
    }

    /**
     * UPDATE Work Order
     */
    public function update(Request $request, $id)
    {
        $workOrder = WorkOrder::with(['pics', 'descriptions'])->findOrFail($id);

        $data = $request->validate([
            'wo_date' => 'sometimes|date',
            'wo_number_in_project' => 'sometimes|integer',
            'wo_kode_no' => 'sometimes|string',
            'total_mandays_eng' => 'sometimes|integer',
            'total_mandays_elect' => 'sometimes|integer',
            'add_work' => 'sometimes|boolean',
            'pics' => 'array',
            'pics.*.user_id' => 'required|exists:users,id',
            'pics.*.role_id' => 'nullable|exists:roles,id',
            'descriptions' => 'array',
            'descriptions.*.description' => 'nullable|string',
            'descriptions.*.result' => 'nullable|string',
        ]);

        DB::transaction(function () use ($workOrder, $data) {
            $workOrder->update($data);

            if (isset($data['pics'])) {
                $workOrder->pics()->delete();
                $workOrder->pics()->createMany($data['pics']);
            }

            if (isset($data['descriptions'])) {
                $workOrder->descriptions()->delete();
                $workOrder->descriptions()->createMany($data['descriptions']);
            }
        });

        return response()->json([
            'status' => 'success',
            'data'   => $workOrder->fresh(['pics.user', 'pics.role', 'descriptions']),
        ]);

    }

    public function getWoSummary()
    {
        $projects = Project::with(['quotation.client', 'statusProject', 'client']) // load relasi
            ->withCount('workOrders') // hitung total wo
            ->orderByRaw('CAST(LEFT(CAST(pn_number AS VARCHAR), 2) AS INT) DESC')
            ->orderByRaw('CAST(SUBSTRING(CAST(pn_number AS VARCHAR), 3, LEN(CAST(pn_number AS VARCHAR)) - 2) AS INT) DESC')
            ->get();

        $summary = $projects->map(function ($project) {
            return [
                'pn_number'      => $project->pn_number,
                'project_number' => $project->project_number,
                'project_name'   => $project->project_name,
                'project_client_name' => $project->client?->name,
                'total_wo'       => $project->work_orders_count, // hasil dari withCount
                'quotation'      => $project->quotation ? [
                    'id'            => $project->quotation->id,
                    'quotation_number' => $project->quotation->quotation_number,
                    'client_name' => $project->quotation->client->name,
                ] : null,
                'status_project' => $project->statusProject ? [
                    'id'   => $project->statusProject->id,
                    'name' => $project->statusProject->name,
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $summary,
        ]);
    }

    public function show($id)
    {
        $workOrder = WorkOrder::with(['pics.user', 'pics.role', 'descriptions'])->find($id);

        if (!$workOrder) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Work Order not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $workOrder,
        ]);
    }



}
