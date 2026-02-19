<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'path',
        'type',
        'imageable_id',
        'imageable_type',
        'disk'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
