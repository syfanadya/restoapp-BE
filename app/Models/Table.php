<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class table extends Model
{
    protected $fillable = [
        'number',
        'status',
        'floor_id',
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
}
