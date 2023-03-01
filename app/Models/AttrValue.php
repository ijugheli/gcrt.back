<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'p_value_id',
        'owner_id',
        'created_by',
        'edited_by',
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
        'status_id',
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

    public function childrenCount()
    {
        return (DB::select(
            'SELECT COUNT(0) as count
                           FROM (SELECT 0
                                  FROM `attr_values`
                                 WHERE attr_id = ? AND p_value_id = ?) a',
            [$this->attr_id, $this->value_id]
        ))[0]->count;
    }

    public function getNode($appendLeaf = false)
    {
        $node = [
            'data' => [
                'value_id' => $this->value_id,
                'title' => $this->value,
                $this->property_id => $this->value,
                'id' => $this->id
            ],
            'label' => $this->value,
            'children' => []
        ];

        if ($appendLeaf) {
            $node['childrenCount'] = $this->childrenCount();
            $node['leaf'] = $node['childrenCount'] <= 0;
        }

        return $node;
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

            if (isset($multi['id']) && isset($multi['name'])) {
                $selected[] = $multi['name'];
                $selectedIDs[] = $multi['id'];
            } else {
                foreach ($multi as $item) {
                    if (!isset($item['id']) || !isset($item['name'])) continue;

                    $selected[] = $item['name'];
                    $selectedIDs[] = $item['id'];
                }
            }

            $this->selected = $selected;
            $this->selectedIDs = $selectedIDs;

            return $this->selected;
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

    // public function scopeActive(Builder $query): void
    // {
    //     $query->where('status_id', 1);
    // }
}
