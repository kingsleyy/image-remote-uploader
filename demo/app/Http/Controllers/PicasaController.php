<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/19/15
 * Time: 4:19 PM
 */

namespace App\Http\Controllers;
use Response;

class PicasaController extends Controller
{
    public function getIndex()
    {
        $links = [
            ['text' => 'My Albums', 'url' => url('picasa/albums') ]
        ];

        return view('picasa.index', ['links' => $links]);
    }

    public function getAlbums()
    {
        $albums = $this->picasa->listAlbums();

        //return \Response::make($albums, 200,['Content-Type' => 'text/xml']);
        $dom = simplexml_load_string($albums);
        $html = '';

        foreach ($dom->entry as $entry) {
            $html .= sprintf('<p><strong>%s</strong> <code>%s</code></p>',
                $entry->title, $entry->children('gphoto',true)->id);
        }
        $headers = [
            'Content-Type' => 'text/html',
        ];
        return Response::make($html, 200, $headers);
    }

    public function getCreateAlbum()
    {
        $response = $this->picasa->createAlbum('test-album-' . time(), 'Test album.');

        return Response::make($response, 200, ['Content-Type' => 'text/json']);
    }

    public function getUpload()
    {
        $json = $this->picasa->doUpload('/tmp/shiro_avatar_square.jpg');

        $content = sprintf('<img src="%s" />', $json);

        return Response::make($content, 200, ['Content-Type' => 'text/html']);
    }
}