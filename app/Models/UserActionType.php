<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActionType extends Model
{
    protected $fillable = [
        'id',
        'type',
        'title'
    ];
}
