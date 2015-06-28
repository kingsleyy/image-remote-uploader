<?php namespace App\Http\Controllers\Manage;

use App\CacheStorage;
use App\Http\Controllers\Controller;
use Config;
use Kings\ImageRemoteUploader\Uploaders\Picasa;

class DefaultController extends Controller
{
    public function getIndex()
    {
        $loginUrl = $this->picasa->getOAuthLoginUrl(url('manage/picasa/callback'));
        $data['picasa'] = $this->picasa;
        $data['login_url'] = $loginUrl;

        return view('manage.index', $data);
    }
}