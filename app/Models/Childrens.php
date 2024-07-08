<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class childrens extends Model
{
    use HasFactory;
    protected $table = 'childrens';
    protected $fillable = [
        'id',
        'name',
        'lastName',
        'secondSurname',
        'age',
        'birtDate',
        'placeWas',
        'Rfc',
        'typeWork',
        'initialSchedule',
        'finalSchedule',
        'tutor',
        'conditions',
        'observations',
        'users_id',
        'active',

    ];
    protected $hidden = [
        'created_at',
        'updated_at',

    ];
}
