<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 2:54 PM
 */

namespace App;


use Cache;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;

class CacheStorage implements StorageInterface
{

    /**
     * Checking for exists a key
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return Cache::has($key);
    }

    /**
     * Get value of a key
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Set value for a key
     * @param string $key
     * @param string|mixed $value
     */
    public function set($key, $value)
    {
        Cache::forever($key, $value);
    }

    /**
     * Remove a key value
     * @param string $key
     * @return mixed
     */
    public function delete($key)
    {
        Cache::forget($key);
    }
}