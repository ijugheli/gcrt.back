<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyRecord extends Model
{
    protected $fillable = ['id', 'survey_id', 'user_id', 'record_id', 'values'];

    protected $casts = ['values' => 'json'];
    protected $with = [
        'definitions'
    ];

    public function survey()
    {
        return $this->belongsTo(Attr::class);
    }

    public function user()
    {
        return $this->belongsTo(Attr::class);
    }

    public function record()
    {
        return $this->hasMany(AttrValue::class, 'value_id', 'record_id');
    }
}
