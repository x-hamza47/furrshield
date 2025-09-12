<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adoption extends Model
{
    use HasFactory;

    protected $fillable = [
        'shelter_id',
        'name',
        'species',
        'breed',
        'age',
        'status',
        'description',
        'image'
    ];

    public function shelter()
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }

    public function requests()
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    public function adopter()
    {
        return $this->hasOneThrough(
        User::class,           
        AdoptionRequest::class,
        'adoption_id',         
        'id',                  
        'id',                  
        'user_id'              
        );
    }
}
