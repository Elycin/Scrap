<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Upload extends Model
{
    protected $fillable = [
        "user_id",
        "resolver_id",
        "original_filename",
        "alias"
    ];


    /**
     * Get the cached upload by the alias.
     *
     * @param string $alias
     * @return Upload
     */
    public static function getCached(string $alias): self
    {
        return Cache::tags('file')->remember(strtolower($alias), config('app.cache_time', 10), function () use ($alias) {
            return self::where('alias', $alias)->firstOrFail();
        });
    }

    /**
     * Remove the entry from the cache
     */
    public function removeFromCache()
    {
        Cache::tags('file')->forget(strtolower($this->alias));
    }

    /**
     * Get Owner ID
     *
     * Get the owner ID.
     *
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->user_id;
    }

    /**
     * Get Resolver ID
     *
     * Returns the id of the resolver.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getResolverId()
    {
        return $this->resolver_id;
    }

    /**
     * Get Alias
     *
     * returns the aliased filename
     *
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Check if the upload has an expiration date.
     *
     * @return bool
     */
    public function hasExpirationDate()
    {
        return isset($this->user_expiration);
    }

    /**
     * Get the date of when the upload expires.
     *
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->user_expiration;
    }

    /**
     * Check to see if the uploaded alias has expired and should be deleted.
     *
     * @return bool
     */
    public function isExpired()
    {
        return Carbon::now()->gt(Carbon::parse($this->getExpirationDate()));
    }


}
