<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReadNotification extends Model
{
    use HasFactory;
    protected $table = 'users_read_notifications';
    protected $fillable = ['id_user', 'system_id', 'roles_id', 'notifications_id'];
}