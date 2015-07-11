<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/10/15
 * Time: 1:41 PM
 */

namespace App\Entities;

/**
 * Class Album
 * @package App\Entities
 *
 * @property string $g_id
 * @property string $name
 * @property int $c_photo
 */
class Album extends BaseEntity
{
    protected $table = 'album';

    public function photos()
    {
        return $this->hasMany(Photo::class, 'album_id');
    }
}