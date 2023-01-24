<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attr extends Model
{
    protected $table = "attrs";

    protected $fillable = ['p_id', 'type', 'status_id', 'title', 'lazy'];
    protected $appends = ['count', 'isTree'];
    protected $casts = [
        'lazy' => 'boolean',
        'status_id' => 'boolean'
    ];
    public $timestamps = false;

    public function values()
    {
        return $this->hasMany(AttrValue::class, 'attr_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany(AttrProperty::class, 'attr_id', 'id');
    }

    public function parent()
    {
        return $this->p_id > 0 ? $this->hasOne(Attr::class, 'id', 'p_id') : null;
    }

    public function getCountAttribute()
    {
        return (DB::select(
            'SELECT COUNT(0) as count
                             FROM (SELECT 0
                                     FROM `attr_values`
                                    WHERE attr_id = ? GROUP BY value_id
                                   ) a',
            [$this->id]
        ))[0]->count;
    }


    public function getIsTreeAttribute()
    {
        return $this->type == config('settings.ATTR_TYPES')['tree'];
    }

    public function hasOptions()
    {
        return $this->type != null && !$this->isEntity();
    }

    public function isEntity()
    {
        return $this->type == config('settings')['ATTR_TYPES']['entity'];
    }
}
