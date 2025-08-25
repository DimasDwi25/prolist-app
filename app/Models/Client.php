<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'client_representative',
        'city',
        'province',
        'country',
        'zip_code',
        'web',
        'notes'
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
