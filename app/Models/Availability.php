<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'availability'; // explicitly define table name

    protected $fillable = [
        'user_id',
        'start',
        'end',
        'note',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
