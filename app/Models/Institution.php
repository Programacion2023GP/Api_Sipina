<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;
    protected $table = 'institutions';
    protected $fillable = [
        'id',
        'name',
        'active'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',

    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
