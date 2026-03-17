<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $primaryKey = 'level';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'level',
        'min_xp',
        'name',
        'badge_id',
    ];
}
