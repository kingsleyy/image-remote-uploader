<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 7/10/15
 * Time: 1:39 PM
 */

namespace App\Entities;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class BaseEntity
 * @package App\Entities
 *
 * @property string $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class BaseEntity extends Model
{

}