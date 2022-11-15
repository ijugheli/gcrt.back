<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttrValue extends Model
{
    protected $table = "attr_values";
    public $timestamps = false;

    protected $appends = [
        'value',
        'value_name'
    ];

    protected $fillable = [
        'property_id',
        'value_id',
        'related_value_id',
        'attr_id',
        'order_id',
        'value_string',
        'value_text',
        'value_integer',
        'value_decimal',
        'value_boolean',
        'value_date',
        'value_json',
    ];

    protected $valueColumns = [
        'value_string',
        'value_text',
        'value_integer',
        'value_decimal',
        'value_boolean',
        'value_date',
        'value_json',
    ];

    public function valueObject()
    {
        return !is_null($this->value_id) ? $this->hasOne(AttrValue::class, 'id', 'value_id') : null;
    }

    public function getValueAttribute()
    {
        if (isset($this->value_json) && !is_null($this->value_json)) {
            $selectedIDs = [];
            $selected = [];
            $multi = json_decode($this->value_json, true);

            if (empty($multi)) {
                return $this->selected;
            }

            foreach ($multi as $item) {
                if (!isset($item['id']) || !isset($item['name'])) continue;

                $selected[] = $item['name'];
                $selectedIDs[] = $item['id'];
            }

            $this->selected = $selected;
            $this->selectedIDs = $selectedIDs;

            return $this['selected'];
        }

        foreach ($this->valueColumns as $column) {
            if (!is_null($this->$column)) {
                return $this->$column;
            }
        }

        return null;
    }

    public function getValueNameAttribute()
    {
        foreach ($this->valueColumns as $column) {
            if (!is_null($this->$column)) {
                return $column;
            }
        }

        return null;
    }
}
