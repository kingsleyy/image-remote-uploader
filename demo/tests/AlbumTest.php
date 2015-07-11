<?php
use App\Repositories\AlbumRepository;

class AlbumTest extends TestCase
{
    public function testChoseAlbum()
    {
        $albumRepo  = App::make(AlbumRepository::class);

        $album = $albumRepo->choseAlbum(10, 'otk', $this->picasa);

        $this->assertInstanceOf(\App\Entities\Album::class, $album);
    }
}