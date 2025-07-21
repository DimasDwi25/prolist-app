<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeOfWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'phc_id',
        'description',
        'category',
        'items',
    ];

    public function phc()
    {
        return $this->belongsTo(PHC::class, 'phc_id');
    }

    // protected $casts = [
    //     'items' => 'array',
    // ];
    

}
