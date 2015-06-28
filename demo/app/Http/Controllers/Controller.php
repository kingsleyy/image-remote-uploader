<?php

namespace App\Http\Controllers;

use App\CacheStorage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Kings\ImageRemoteUploader\Uploaders\Picasa;
use Config;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    /**
     * @var Picasa
     */
    protected $picasa;

    public function __construct()
    {
        $this->picasa = new Picasa(new CacheStorage());

        $this->picasa->setApiKey(Config::get('picasa.key'));
        $this->picasa->setSecret(Config::get('picasa.secret'));
    }
}
