<?php
/**
 * Created by PhpStorm.
 * User: kings
 * Date: 6/18/15
 * Time: 10:43 AM
 */

namespace Kings\ImageRemoteUploader\Uploaders;


use File;
use GuzzleHttp\Client;
use Kings\ImageRemoteUploader\AbstractUploader;
use Kings\ImageRemoteUploader\Exceptions\CreateAlbumFailedException;
use Kings\ImageRemoteUploader\Exceptions\ExchangeTokenErrorException;
use Kings\ImageRemoteUploader\Exceptions\FileUploadFailedException;

class Picasa extends AbstractUploader
{
    const OAUTH_ENDPOINT = 'https://accounts.google.com/o/oauth2/auth';
    const OAUTH_TOKEN_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/token';
    const OAUTH_SCOPE = 'https://picasaweb.google.com/data/';

    const USER_FEED_ENDPOINT = 'https://picasaweb.google.com/data/feed/api/user/%s';
    const ALBUM_FEED_ENDPOINT = 'https://picasaweb.google.com/data/feed/api/user/%s/albumid/%s';

    protected $name = 'picasa';

    /**
     * Get a url for redirect to login (get access token)
     * @param string $callback
     * @return string
     */
    public function getOAuthLoginUrl($callback)
    {
        $this->checkApiKey();
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->apiKey,
            'redirect_uri' => $callback,
            'scope' => static::OAUTH_SCOPE,
            'state' => 'request_token',
            'approval_prompt' => 'force',
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
        );

        return static::OAUTH_ENDPOINT . '?' . http_build_query($params);
    }

    /**
     * Get access token (after redirect from login page of provider)
     * @param string $code
     * @param string $callback
     * @throws ExchangeTokenErrorException
     */
    public function getAccessTokenAfterLogin($code, $callback)
    {
        $client = new Client();
        $params = [
            'code' => $code,
            'client_id' => $this->apiKey,
            'client_secret' => $this->secret,
            'redirect_uri' => $callback,
            'grant_type' => 'authorization_code',
        ];
        $response = $client->post(static::OAUTH_TOKEN_ENDPOINT, ['form_params' => $params]);
        $result = json_decode($response->getBody(true), true);

        if (array_key_exists('error', $result)) {
            throw new ExchangeTokenErrorException($result['error'], $result['error_description']);
        }

        $this->saveToken(
            $result['access_token'],
            $result['token_type'],
            $result['expires_in'] + time(),
            $result['refresh_token']);
    }

    /**
     * Upload a file
     * @param string $filepath
     * @param string $album
     * @return string
     * @throws FileUploadFailedException
     */
    public function doUpload($filepath, $album = 'default')
    {
        $client = new Client();
        $headers = $this->makeHeaders();
        $headers['Content-Type'] = File::mimeType($filepath);
        $headers['Content-Length'] = File::size($filepath);
        $headers['Slug'] = File::name($filepath);

        $response = $client->post('https://picasaweb.google.com/data/feed/api/user/default/albumid/default?alt=json', [
            'headers' => $headers,
            'body' => fopen($filepath, 'r'),
        ]);

        if ($response->getStatusCode() != 201) {
            throw new FileUploadFailedException;
        }

        $jsonResponse = (string)$response->getBody(true);
        $obj = json_decode($jsonResponse);

        return $this->transferBetterImageUrl($obj->entry->content->src);
    }

    /**
     * Create an album if support
     * @param string $album_name
     * @param string $description
     * @return mixed
     * @throws CreateAlbumFailedException
     */
    public function createAlbum($album_name, $description)
    {
        $xmlData = <<<XML
<entry xmlns='http://www.w3.org/2005/Atom'
    xmlns:media='http://search.yahoo.com/mrss/'
    xmlns:gphoto='http://schemas.google.com/photos/2007'>
  <title type='text'>$album_name</title>
  <summary type='text'>$description</summary>
  <gphoto:location>Vietnam</gphoto:location>
  <gphoto:access>public</gphoto:access>
  <media:group>
    <media:keywords>vietnam, otakufc, manga</media:keywords>
  </media:group>
  <category scheme='http://schemas.google.com/g/2005#kind'
    term='http://schemas.google.com/photos/2007#album'></category>
</entry>
XML;

        $client = new Client();
        $response = $client->post('https://picasaweb.google.com/data/feed/api/user/default?alt=json', [
            'headers' => $this->makeHeaders(),
            'body' => $xmlData,
        ]);

        if ($response->getStatusCode() != 201) {
            throw new CreateAlbumFailedException;
        }

        $jsonBody = (string)$response->getBody(true);
        $object = json_decode($jsonBody, true);

        return $object['gphoto$id'];
    }

    /**
     * Make headers for send request to provider
     * @return array
     */
    function makeHeaders()
    {
        $auth = sprintf('%s %s', $this->getTokenType(), $this->getTokenAccessKey());

        return array(
            'Authorization' => $auth,
            'GData-Version' => '2',
            'MIME-version' => '1.0',
            'Content-Type' => 'application/atom+xml',
        );
    }

    /**
     * Refresh expired token
     * @throws ExchangeTokenErrorException
     */
    public function refreshToken()
    {
        $client = new Client();
        $params = [
            'refresh_token' => $this->getRefreshToken(),
            'client_id' => $this->apiKey,
            'client_secret' => $this->secret,
            'grant_type' => 'refresh_token',
        ];

        $response = $client->post(static::OAUTH_TOKEN_ENDPOINT, ['form_params' => $params]);
        $result = json_decode($response->getBody(true), true);
        if (array_key_exists('error', $result)) {
            throw new ExchangeTokenErrorException($result['error'], $result['error_description']);
        }

        $this->saveToken(
            $result['access_token'],
            $result['token_type'],
            $result['expires_in'] + time(),
            $this->getRefreshToken());
    }

    protected function transferBetterImageUrl($url)
    {
        $pattern = '/([a-zA-Z0-9])*.(ggpht|googleusercontent).com/';
        $replace = (date('m') % 4 + 1) . ".bp.blogspot.com";
        $url = preg_replace($pattern, $replace, $url);

        return $url;
    }

    /**
     * Get list album if support
     * @return string xml
     */
    public function listAlbums()
    {
        $client = new Client();
        $headers = $this->makeHeaders();

        $response = $client->get('https://picasaweb.google.com/data/feed/api/user/default', [
            'headers' => $headers
        ]);

        return (string)$response->getBody(true);
    }
}