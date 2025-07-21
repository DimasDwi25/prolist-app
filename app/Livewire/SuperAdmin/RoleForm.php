<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Role;
use Livewire\Component;

class RoleForm extends Component
{
    public $name;
    public $type_role;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type_role' => 'required|in:1,2',
    ];

    public function save()
    {
        $this->validate();

        Role::create([
            'name' => $this->name,
            'type_role' => $this->type_role,
        ]);

        session()->flash('success', 'Role successfully created!');
        return redirect()->route('admin.role');
    }

    public function render()
    {
        return view('livewire.super-admin.role-form');
    }
}
