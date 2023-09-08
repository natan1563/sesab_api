<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'cep',
        'public_place',
        'neighborhood',
        'locality',
        'uf',
        'user_id'
    ];

    public function address()
    {
        return $this->belongsTo(User::class);
    }
}
