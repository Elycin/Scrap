<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        "user_id",
        "resolver_id",
        "original_filename",
        "alias"
    ];


}
