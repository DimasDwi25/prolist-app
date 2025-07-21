<?php

namespace App\Livewire;

use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Project;
use App\Models\Client;
use App\Models\WorkOrder;

class WorkOrderForm extends Component
{
    public $project_id;
    public $wo_kode_no;
    public $wo_date;
    public $pic1, $pic2, $pic3, $pic4, $pic5;
    public $role_pic_1, $role_pic_2, $role_pic_3, $role_pic_4, $role_pic_5;
    public $total_mandays_eng = 0;
    public $total_mandays_elect = 0;
    public $add_work = false;
    public $work_description;

    public $showLogModal = false;
    public $wo_number_in_project;
    public $client_name;

    public function updatedProjectId($value)
    {
        $project = Project::with('client')->find($value);

        if ($project) {
            $this->client_name = $project->client->name ?? null;

            // Hitung jumlah WO di project ini + 1
            $countWO = WorkOrder::where('project_id', $value)->count() + 1;
            $this->wo_number_in_project = $countWO;

            // Tahun: ambil 2 digit terakhir
            $year = now()->format('y'); // misalnya: 24

            // Format project_number menjadi 3 digit
            $projectNumber = str_pad($project->project_number, 3, '0', STR_PAD_LEFT); // 7 jadi 007

            // Format akhir
            $this->wo_kode_no = 'WO-' . $year . '/' . $projectNumber . '/' . $countWO;
        }
    }


    public function save()
    {
        $this->updatedProjectId($this->project_id); // Refresh kode WO

        $this->validate([
            'project_id' => 'required',
            'wo_date' => 'required|date',
            'wo_kode_no' => 'required',
        ]);

        $wo = WorkOrder::create([
            'project_id' => $this->project_id,
            'wo_date' => $this->wo_date,
            'wo_number_in_project' => $this->wo_number_in_project,
            'wo_kode_no' => $this->wo_kode_no,
            'pic1' => $this->pic1,
            'pic2' => $this->pic2,
            'pic3' => $this->pic3,
            'pic4' => $this->pic4,
            'pic5' => $this->pic5,
            'role_pic_1' => $this->role_pic_1,
            'role_pic_2' => $this->role_pic_2,
            'role_pic_3' => $this->role_pic_3,
            'role_pic_4' => $this->role_pic_4,
            'role_pic_5' => $this->role_pic_5,
            'total_mandays_eng' => $this->total_mandays_eng,
            'total_mandays_elect' => $this->total_mandays_elect,
            'add_work' => $this->add_work,
            'work_description' => $this->work_description,
        ]);

        $this->showLogModal = true;
    }

    public function confirmAddToLog()
    {
        Log::create([
            'project_id' => $this->project_id,
            'users_id' => Auth::id(),
            'logs' => $this->work_description,
            'tgl_logs' => now(),
            'status' => 'open',
            'categorie_log_id' => 1, // sesuaikan jika perlu
        ]);

        session()->flash('success', 'Work Order & Log berhasil ditambahkan!');
        return redirect()->route('work-orders.index');
    }

    public function render()
    {
        return view('livewire.work-order-form', [
            'projects' => Project::latest()->take(5)->get(),
            'roles' => Role::where('type_role', 2)->get(),
            'users' => User::all(),
        ]);
    }
}
