<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/11/15
 * Time: 8:45 AM
 */

namespace App\Threads;
use App\Storage\DatabaseStorage;
use Config;
use Kings\ImageRemoteUploader\Contracts\UploaderInterface;
use Kings\ImageRemoteUploader\Uploaders\Picasa;
use Stackable;
use Worker as PhpWorker;

class UploadThread extends PhpWorker
{
    /**
     * @var UploaderInterface
     */
    public $uploader;

    /**
     * @var Stackable
     */
    protected $data;

    /**
     * @var Stackable
     */
    protected $results;

    protected $stopFlag = false;

    /**
     * @param Stackable $data
     * @param Stackable $results
     */
    public function __construct(&$data, &$results)
    {
        $this->data = $data;
        $this->results = $results;
    }

    /**
     * Flag for stop thread
     */
    public function terminate()
    {
        $this->stopFlag = true;
    }

    public function run()
    {
        echo "@New thread started.\n";
        $storage = new DatabaseStorage();
        $this->uploader = new Picasa($storage);
//
//        $this->uploader->setApiKey(Config::get('picasa.key'));
//        $this->uploader->setSecret(Config::get('picasa.secret'));

        while (1) {
            echo "@Enter loop.\n";
            /** @var UploadInfo $uploadInfo */
            $uploadInfo = $this->data->shift();
            if ($uploadInfo) {
                echo " + Uploading {$uploadInfo->getFilepath()} ... ";
                $this->results[] = $this->uploader->doUpload($uploadInfo->getFilepath(), $uploadInfo->getAlbum());
                echo "OK\n";
            } else {
                echo " #Nodata, waiting ...\n";
                // Waiting for data 0.1s
                usleep(100000);
            }
        }
    }
}