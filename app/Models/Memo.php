<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memo extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $hidden = [
        'user_id',
        'user_name',
        'title',
        'tag',
        'deleted_at',
    ];
}
