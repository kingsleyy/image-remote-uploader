<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/11/15
 * Time: 8:58 AM
 */

namespace App\Threads;

/**
 * Class UploadInfo
 *
 * Just wrap data for upload thread
 * @package App\Threads
 */
class UploadInfo
{
    /**
     * @var string
     */
    protected $filepath;
    /**
     * @var string
     */
    protected $album;

    /**
     * @param string $filepath
     * @param string $album
     */
    public function __construct($filepath, $album)
    {
        $this->filepath = $filepath;
        $this->album = $album;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @return string
     */
    public function getAlbum()
    {
        return $this->album;
    }
}