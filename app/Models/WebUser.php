<?php

// phpcs:ignoreFile

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebUser extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'isNotified'];
}
