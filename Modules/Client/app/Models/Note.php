<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;

class Note extends Model
{
    use HasFactory;
     protected $fillable = [
        'sender_id',
        'reciever_id',
        'note',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }

    public function reciever()
    {
        return $this->belongsTo(User::class,'reciever_id');
    }
}
