<?php

namespace App\Providers;

use App\Storage\DatabaseStorage;
use Config;
use Illuminate\Support\ServiceProvider;
use Kings\ImageRemoteUploader\Uploaders\Picasa;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('PicasaUploader', function() {
            $storage = new DatabaseStorage();
            $picasa = new Picasa($storage);

            $picasa->setApiKey(Config::get('picasa.key'));
            $picasa->setSecret(Config::get('picasa.secret'));

            return $picasa;
        });
    }
}
