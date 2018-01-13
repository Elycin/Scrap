<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileResolver extends Model
{
    protected $table = "file_resolver";
    public static $hash_method = "sha256";
    protected $fillable = [
        "hash",
        "mime",
        "size",
        "encrypted_size",
        "encrypted"
    ];
}
