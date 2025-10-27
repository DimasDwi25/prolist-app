<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhcApproval extends Model
{
    use HasFactory, ActivityLoggable;

    protected $fillable = [
        'phc_id',
        'user_id',
        'status',
        'validated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phc()
    {
        return $this->belongsTo(PHC::class);
    }
}
