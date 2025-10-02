<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPreparation extends Model
{
    use HasFactory;

     protected $fillable = ['document_id', 'phc_id', 'is_applicable', 'date_prepared'];

    public function document()
    {
        return $this->belongsTo(DocumentPhc::class, 'document_id');
    }

    public function phc()
    {
        return $this->belongsTo(PHC::class, 'phc_id');
    }

    protected $casts = [
        'date_prepared' => 'date:Y-m-d',
        'is_applicable' => 'boolean',
    ];
}
