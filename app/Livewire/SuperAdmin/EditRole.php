<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Role;
use Livewire\Component;

class EditRole extends Component
{
    public $role_id;
    public $name;
    public $type_role;

    public function mount($role)
    {
        $data = \App\Models\Role::findOrFail($role);

        $this->role_id = $data->id;
        $this->name = $data->name;
        $this->type_role = $data->type_role;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type_role' => 'required|in:1,2',
        ]);

        \App\Models\Role::where('id', $this->role_id)->update([
            'name' => $this->name,
            'type_role' => $this->type_role,
        ]);

        session()->flash('success', 'Role updated successfully!');
        return redirect()->route('admin.role');
    }

    public function render()
    {
        return view('livewire.super-admin.edit-role')
            ->layout('admin.layouts.app');
    }
}
