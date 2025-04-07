<?php

// phpcs:ignoreFile

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
           'title',
           'description',
           'category',
           'image_id',   // Cloudinary image public id
           'image_url',  // Cloudinary image URL
       ];
    //
}
