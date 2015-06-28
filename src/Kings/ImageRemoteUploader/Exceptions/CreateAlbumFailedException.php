<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/27/15
 * Time: 4:29 PM
 */

namespace Kings\ImageRemoteUploader\Exceptions;
use Exception;

class CreateAlbumFailedException extends Exception
{
    protected $message = 'Create album failed.';
}