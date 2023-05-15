<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Attr extends Model
{
    protected $table = "attrs";
    protected $fillable = ['p_id', 'type', 'status_id', 'title', 'lazy'];
    protected $appends = ['count', 'isTree'];
    protected $casts = [
        'lazy' => 'boolean',
        // 'status_id' => 'boolean'
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

    public static function hasChildren($values)
    {
        return  DB::table('attr_values as av')
            ->whereIn('p_value_id', $values->pluck('value_id')->toArray())
            ->where('attr_id', $values[0]['attr_id'])
            ->select('p_value_id', DB::raw('EXISTS(SELECT 1 FROM attr_values WHERE p_value_id = av.p_value_id) as has_child'))
            ->groupBy('p_value_id')
            ->pluck('has_child', 'p_value_id');
    }

    public function hasOptions()
    {
        return $this->type != null && !$this->isEntity();
    }

    public function isEntity()
    {
        return $this->type == config('settings')['ATTR_TYPES']['entity'];
    }

    // public function scopeActive(Builder $query): void
    // {
    //     $query->where('status_id', 1);
    // }
}
