<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'pet_id',
        'vet_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
        'lab_reports',
        'visit_date',
    ];

    protected $casts = [
        'lab_reports' => 'array', 
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
