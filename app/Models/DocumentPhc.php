<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPhc extends Model
{
    use HasFactory, ActivityLoggable;

   protected $table = 'documents_phc';

    protected $fillable = ['name'];

    public function preparations()
    {
        return $this->hasMany(DocumentPreparation::class, 'document_id');
    }
}
