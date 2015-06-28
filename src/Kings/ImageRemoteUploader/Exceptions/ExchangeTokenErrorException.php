<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 11:30 AM
 */

namespace Kings\ImageRemoteUploader\Exceptions;
use Exception;

class ExchangeTokenErrorException extends Exception
{
    public function __construct($error, $description)
    {
        $message = sprintf('Exchange token problem: %s -> %s', $error, $description);
        parent::__construct($message);
    }
}