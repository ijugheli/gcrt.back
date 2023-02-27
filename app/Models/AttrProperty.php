<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttrProperty extends Model
{
    protected $table = "attr_properties";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'p_id',
        'source_attr_id',
        'title',
        'input_data_type',
        'input_view_type',
        'is_mandatory',
        'has_filter',
        'is_primary',
        'attr_id',
        'type',
        'order_id',
        'status_id',
    ];
    public function dataType()
    {
        return $this->hasOne(InputDataType::class, 'id', 'input_data_type');
    }

    public function viewType()
    {
        return $this->hasOne(InputViewType::class, 'id', 'input_view_type');
    }

    public function source()
    {
        return $this->hasOne(Attr::class, 'id', 'source_attr_id');
    }

    public function isSection()
    {
        return  $this->type == 2;
    }

    public function remove() {
        return $this->update(['status_id' => -1]);
    }
}
