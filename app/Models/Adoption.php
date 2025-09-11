<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adoption extends Model
{
    use HasFactory;

    protected $fillable = ['shelter_id', 'name', 'species', 'breed', 'age', 'status', 'description'];

    public function shelter()
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }
}
