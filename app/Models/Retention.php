<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retention extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'project_id',
        'retention_due_date',
        'retention_value',
        'invoice_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'pn_number');
    }
}
