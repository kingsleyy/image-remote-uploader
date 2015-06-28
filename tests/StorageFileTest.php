<?php
use Kings\ImageRemoteUploader\FileStorage;

/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 2:06 PM
 */

class StorageFileTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFile()
    {
        $file = sys_get_temp_dir() . '/my_storage_' .time();
        $storage = new FileStorage($file);
        $storage->set('name', 'Kings');

        $this->assertFileExists($file);
    }

    public function testSetRemoveValue()
    {
        $file = sys_get_temp_dir() . '/my_storage_' .time();
        $storage = new FileStorage($file);
        $key = 'test_key_' . rand(1, 1000);
        $value = rand(1, 100000);
        $storage->set($key, $value);

        $this->assertTrue($storage->has($key));
        $this->assertEquals($value, $storage->get($key));

        $storage->delete($key);
        $this->assertFalse($storage->has($key));
    }
}