<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shelter extends Model
{
    use HasFactory;

    protected $fillable = ['shelter_id', 'shelter_name', 'contact_person', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }

    public function adoptionListings()
    {
        return $this->hasMany(Adoption::class, 'shelter_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'shelter_id');
    }
}
