<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'user_id',
        'action_type',
        'action_description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

