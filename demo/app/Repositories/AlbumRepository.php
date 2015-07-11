<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/10/15
 * Time: 1:48 PM
 */

namespace App\Repositories;
use App\Entities\Album;
use Carbon\Carbon;
use DB;
use Kings\ImageRemoteUploader\Uploaders\Picasa;

class AlbumRepository
{
    public function truncateDatabase()
    {
        DB::table('photo')->delete();
        DB::table('album')->delete();
    }

    public function insertRawData($array)
    {
        foreach ($array as &$item) {
            if (!array_key_exists('created_at', $item)) {
                $item['created_at'] = Carbon::now();
            }
            $item['updated_at'] = Carbon::now();
        }

        DB::table('album')->insert($array);
    }

    /**
     * Chose best album for upload more $max photos
     * @param int $max
     * @param string $prefix
     * @param Picasa $picasaRepo
     * @return Album
     * @throws \Kings\ImageRemoteUploader\Exceptions\CreateAlbumFailedException
     */
    public function choseAlbum($max, $prefix = '%', $picasaRepo = null)
    {
        $album = Album::where('name', 'LIKE', $prefix . '%')
            ->whereRaw(sprintf('2000 - c_photo >= %d', $max))
            ->first();
        // Create album
        if ($picasaRepo && !$album) {
            $name = uniqid($prefix);
            $id = $picasaRepo->createAlbum($name, 'New album for upload.');

            $album = new Album();
            $album->g_id = $id;
            $album->name = $name;
            $album->c_photo = 0;
            $album->save();
        }

        return $album;
    }
}