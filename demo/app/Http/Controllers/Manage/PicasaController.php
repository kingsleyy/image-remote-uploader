<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 4:05 PM
 */

namespace App\Http\Controllers\Manage;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PicasaController   extends Controller
{
    public function getIndex()
    {

    }

    public function getCallback(Request $request)
    {
        $this->picasa->getAccessTokenAfterLogin($request->get('code'),
            url('manage/picasa/callback'));

        return redirect('manage');
    }

    public function getRefresh()
    {
        $this->picasa->refreshToken();
        return redirect('manage');
    }
}