<?php

// phpcs:ignoreFile

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
           'title',
           'description',
           'icon',
           'image_id',   // Cloudinary image public id
           'image_url',  // Cloudinary image URL
       ];
    //
}
