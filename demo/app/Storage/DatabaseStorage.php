<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/11/15
 * Time: 8:01 AM
 */

namespace App\Storage;


use App\Entities\TokenStorage;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;

class DatabaseStorage
    implements StorageInterface
{
    /**
     * @param $key
     * @return TokenStorage
     */
    protected function getObject($key)
    {
        return TokenStorage::whereKey($key)->first();
    }

    /**
     * Checking for exists a key
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return TokenStorage::whereKey($key)->count() > 0;
    }

    /**
     * Get value of a key
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $object = $this->getObject($key);

        return $object ? unserialize($object->value) : $default;
    }

    /**
     * Set value for a key
     * @param string $key
     * @param string|mixed $value
     * @return self
     */
    public function set($key, $value)
    {
        $object = $this->getObject($key);
        if (!$object) {
            $object = new TokenStorage();
            $object->key = $key;
        }

        $object->value = serialize($value);
        $object->save();

        return $this;
    }

    /**
     * Remove a key value
     * @param string $key
     * @return mixed
     */
    public function delete($key)
    {
        $object = $this->getObject($key);
        if ($object) {
            $object->delete();
        }

        return $this;
    }
}