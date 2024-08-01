<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Friend extends Model {

    use HasFactory;

    public $timestamps = true;
    protected $fillable =[
        'id',
        'sender_id',
        'receiver_id',
        'status',
    ];

}