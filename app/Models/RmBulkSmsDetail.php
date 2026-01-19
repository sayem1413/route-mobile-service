<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmBulkSmsDetail extends Model
{
    protected $fillable = [
        'to',
        'sender',
        'message',
        'message_id',
        'status',
        'response'
    ];

    protected $casts = [
        'response' => 'array'
    ];
}
