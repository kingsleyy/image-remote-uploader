<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 11:17 AM
 */

namespace Kings\ImageRemoteUploader\Exceptions;
use Exception;

class ApiKeyNotSuppliedException extends Exception
{
    protected $code = -2;
    protected $message = 'Please supply api key & secret to authenticate.';
}