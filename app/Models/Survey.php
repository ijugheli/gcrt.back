<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $table = 'surveys';
    protected $fillable = ['id', 'attr_id', 'title'];
    protected $with = [
        'definitions'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attr::class);
    }

    public function definitions()
    {
        return $this->hasMany(SurveyDefinition::class);
    }
}
