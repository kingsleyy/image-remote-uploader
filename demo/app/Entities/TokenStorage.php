<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/10/15
 * Time: 4:48 PM
 */

namespace App\Entities;

/**
 * Class TokenStorage
 * @package App\Entities
 *
 * @property string $key
 * @property string $value
 */
class TokenStorage extends BaseEntity
{
    protected $table = 'token_storage';
}