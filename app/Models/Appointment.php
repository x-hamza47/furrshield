<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    // protected $fillable = ['pet_id', 'owner_id', 'vet_id', 'appt_date', 'appt_time', 'status'];
    protected $guarded = [];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
