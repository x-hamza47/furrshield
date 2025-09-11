<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'species', 'breed', 'age', 'medical_history'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class, 'pet_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'pet_id');
    }
}
