<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieProject extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = "project_categories";

    protected $fillable = [
        'name',
        'description'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'categories_project_id');
    }
}
