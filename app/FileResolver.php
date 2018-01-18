<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    /**
     * Get Cached Eloquent Data From Upload
     *
     * Returns the cached data of this model.
     *
     * @param Upload $upload
     * @return FileResolver
     */
    public static function getCachedFromUpload(Upload $upload): self
    {
        return Cache::tags('resolver_model')->remember(strtolower($upload->getAlias()), config('app.cache_time', 10), function () use ($upload) {
            return self::where('id', $upload->getResolverId())->firstOrFail();
        });
    }

    /**
     * Get Encrypted Size
     *
     * Returns the encrypted file size column
     *
     * @return mixed
     */
    public function getEncryptedSize()
    {
        return $this->encrypted_size;
    }

    /**
     * Get Size
     *
     * Returns the file size column
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get Mime
     *
     * Returns the file mime column
     *
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Is File Encrypted
     *
     * Returns a boolean of the encrypted column
     *
     * @return mixed
     */
    public function isEncrypted()
    {
        return $this->encrypted;
    }

    /**
     * Get Hash
     *
     * Returns the sha256 hash of the data stream
     *
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Get hash
     *
     * Gets the hash path of the file
     *
     * @return string
     */
    public function getHashPath()
    {
        return sprintf("files/%s", $this->getHash());
    }

}
