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


    public static function getCached(string $alias): self
    {
        return Cache::tags('file_model')->remember(strtolower($alias), config('app.cache_time', 10), function () use ($alias) {
            return self::where('alias', $alias)->firstOrFail();
        });
    }

    /**
     * Remove the entry from the cache
     */
    public function uncache()
    {
        Cache::tags('file_model')->forget(strtolower($this->alias));
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

    public function hasUserDefinedExpirationDate()
    {
        return isset($this->user_expiration);
    }

    public function getUserDefinedExpirationDate()
    {
        return $this->user_expiration;
    }

    public function userDefinedExpirationDateIsExpired()
    {
        return Carbon::now()->gt(Carbon::parse($this->getUserDefinedExpirationDate()));
    }


}
