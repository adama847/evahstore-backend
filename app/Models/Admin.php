<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;  // ← indispensable

    protected $fillable = ['username', 'password'];
    protected $hidden   = ['password'];
}