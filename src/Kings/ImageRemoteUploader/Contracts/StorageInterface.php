<?php
namespace Kings\ImageRemoteUploader\Contracts;

/**
 * Interface for storage bussiness (use store token)
 */
interface StorageInterface
{
    /**
     * Checking for exists a key
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Get value of a key
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set value for a key
     * @param string $key
     * @param string|mixed $value
     * @return
     */
    public function set($key, $value);

    /**
     * Remove a key value
     * @param string $key
     * @return mixed
     */
    public function delete($key);
}
