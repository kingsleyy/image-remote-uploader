<?php
namespace Kings\ImageRemoteUploader\Contracts;

use Kings\ImageRemoteUploader\Exceptions\ExchangeTokenErrorException;

interface UploaderInterface
{
    const TOKEN_EXPIRES_CACHE_NAME = 'image_remote_uploader_token_timeout';
    const TOKEN_ACCESS_KEY_CACHE_NAME = 'image_remote_uploader_token_key';
    const TOKEN_TYPE_CACHE_NAME = 'image_remote_uploader_token_type';
    const TOKEN_REFRESH_CACHE_NAME = 'image_remote_uploader_token_refresh';

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
     * @throws ExchangeTokenErrorException
     */
    public function getAccessTokenAfterLogin($code, $callback);

    /**
     * Upload a file
     * @param string $filepath
     * @param string $album
     * @return
     */
    public function doUpload($filepath, $album = 'defaults');

    /**
     * Create an album if support
     * @param string $album_name
     * @param string $description
     * @return mixed
     */
    public function createAlbum($album_name, $description);

    /**
     * Get list album if support
     * @return string xml
     */
    public function listAlbums();

    /**
     * Make headers for send request to provider
     * @return array
     */
    function makeHeaders();

    /**
     * Refresh expired token
     * @return void
     */
    public function refreshToken();
}
