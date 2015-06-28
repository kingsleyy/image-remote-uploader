<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 2:01 PM
 */

namespace Kings\ImageRemoteUploader;


use Kings\ImageRemoteUploader\Contracts\StorageInterface;

class FileStorage implements StorageInterface
{
    protected $data;
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
        if (file_exists($file)) {
            $this->data = unserialize(file_get_contents($file));
        } else {
            $this->data = [];
        }
    }
    /**
     * Checking for exists a key
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get value of a key
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Set value for a key
     * @param string $key
     * @param string|mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        $this->writeFile();

        return $this;
    }

    /**
     * Remove a key value
     * @param string $key
     * @return mixed
     */
    public function delete($key)
    {
        unset($this->data[$key]);
        $this->writeFile();
    }

    /**
     * Serialize data and write to file
     */
    protected function writeFile()
    {
        $output = serialize($this->data);
        file_put_contents($this->file, $output);
    }
}