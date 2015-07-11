<?php

namespace App\Http\Controllers;

use App\CacheStorage;
use App\Repositories\AlbumRepository;
use App\Storage\DatabaseStorage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;
use Kings\ImageRemoteUploader\Uploaders\Picasa;
use Config;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    /**
     * @var Picasa
     */
    protected $picasa;

    /**
     * @var AlbumRepository
     */
    protected $albumRepo;

    public function __construct()
    {
        $this->picasa = \App::make('PicasaUploader');
        $this->albumRepo = \App::make(AlbumRepository::class);
    }
}
