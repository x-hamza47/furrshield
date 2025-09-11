<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vet extends Model
{
    use HasFactory;

    protected $fillable = ['vet_id', 'specialization', 'experience', 'available_slots'];

    public function user()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
