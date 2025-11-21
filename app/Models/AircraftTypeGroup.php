<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftTypeGroup extends Model
{
    protected $fillable = ['name'];

    public function types()
    {
        return $this->belongsToMany(AircraftType::class, 'aircraft_type_group_items');
    }
}
