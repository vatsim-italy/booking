<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftType extends Model
{
    protected $fillable = ['type'];

    public function groups()
    {
        return $this->belongsToMany(AircraftTypeGroup::class, 'aircraft_type_group_items');
    }
}
