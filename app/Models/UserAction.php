<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'action_type_id',
        'attr_id',
        'record_id',
        'property_id'
    ];

    public function userActionType()
    {
        return $this->hasOne(UserActionType::class, 'id', 'action_type_id');
    }

    public function user()
    {
        return $this->belongsTo(Attr::class);
    }

    public function attr()
    {
        return $this->belongsTo(Attr::class);
    }

    public function record()
    {
        return $this->belongsTo(AttrValue::class, 'value_id', 'record_id');
    }

    public function property()
    {
        return $this->belongsTo(AttrProperty::class);
    }
}
