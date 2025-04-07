<?php

// phpcs:ignoreFile

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    //
    protected $fillable = [
           'name',
           'company',
           'message',
           'image_id',   // Cloudinary image public id
           'image_url',  // Cloudinary image URL
           'rating',
       ];
}
