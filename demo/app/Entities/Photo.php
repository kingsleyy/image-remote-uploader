<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/10/15
 * Time: 1:44 PM
 */

namespace App\Entities;

/**
 * Class Photo
 * @package App\Entities
 *
 * @property string $tags
 * @property string $g_id
 * @property string $g_url
 * @property int $album_id
 * @property-read Album $album
 */
class Photo extends BaseEntity
{
    protected $table = 'photo';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function album()
    {
        return $this->belongsTo(Album::class, 'album_id');
    }
}