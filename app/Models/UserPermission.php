<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    protected $table = "user_permissions";

    protected $fillable = ['id', 'user_id', 'attr_id', 'can_view', 'can_update', 'can_delete', 'can_edit_structure'];

    protected $casts = [
        'can_view' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_edit_structure' => 'boolean'
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attribute(): HasOne
    {
        return $this->hasOne(Attr::class);
    }
}
