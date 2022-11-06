<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attr extends Model
{
    protected $table = "attrs";

    protected $fillable = ['p_id', 'status_id', 'title'];

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
}
