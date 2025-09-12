<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdoptionRequest extends Model
{
    use HasFactory;

    protected $fillable = ['adoption_id', 'user_id', 'status', 'message'];

    public function adoption()
    {
        return $this->belongsTo(Adoption::class);
    }

    public function adopter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
