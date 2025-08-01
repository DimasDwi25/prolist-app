<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhcApproval extends Model
{
    use HasFactory;

    protected $fillable = ['phc_id', 'user_id', 'status', 'validated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phc()
    {
        return $this->belongsTo(PHC::class);
    }
}
