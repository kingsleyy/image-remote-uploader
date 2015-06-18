<?php
namespace Kings\ImageRemoteUploader;

use Kings\ImageRemoteUploader\Contracts\UploaderInterface;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;


abstract class AbstractUploader implements UploaderInterface
{
    /** @var StorageInterface */
    protected $storage;

    /**
     * Used to determind uploader name & set cache key
     * @var string
     */
    protected $name = 'abstract_uploader';

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function hasValidToken()
    {

    }
}
