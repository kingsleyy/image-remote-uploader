<?php
namespace Kings\ImageRemoteUploader\Contracts;

interface UploaderInterface
{
    /**
     * Checking for Uploader has a valid token
     */
    public function hasValidToken();

    /**
     * Get a url for redirect to login (get access token)
     * @param string $callback
     * @return string
     */
    public function getOAuthLoginUrl($callback);

    /**
     * Get access token (after redirect from login page of provider)
     * @param string $code
     * @param string $callback
     */
    public function getAccessTokenAfterLogin($code, $callback);

    /**
     * Upload a file
     * @param string $filepath
     */
    public function doUpload($filepath);
}
