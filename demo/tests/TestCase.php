<?php

use App\Storage\DatabaseStorage;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;
use Kings\ImageRemoteUploader\Uploaders\Picasa;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://image-uploader.dev';

    /**
     * @var Picasa
     */
    protected $picasa;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $this->picasa = App::make('PicasaUploader');

        return $app;
    }
}
