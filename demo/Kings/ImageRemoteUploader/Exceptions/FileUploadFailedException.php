<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/27/15
 * Time: 10:53 PM
 */

namespace Kings\ImageRemoteUploader\Exceptions;
use Exception;

class FileUploadFailedException extends Exception
{
    protected $message = 'Upload file failed.';
}