<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = [
        'creator_id',
        'receiver_id'
    ];
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

}
