<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserValidationCode extends Model
{

    protected $fillable = ['id', 'user_id', 'code', 'action_type', 'validation_type', 'created_at', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
