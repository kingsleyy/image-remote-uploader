<?php
namespace Kings\ImageRemoteUploader;

use GuzzleHttp\Client;
use Kings\ImageRemoteUploader\Contracts\UploaderInterface;
use Kings\ImageRemoteUploader\Contracts\StorageInterface;
use Kings\ImageRemoteUploader\Exceptions\ApiKeyNotSuppliedException;


abstract class AbstractUploader implements UploaderInterface
{
    /** @var StorageInterface */
    protected $storage;

    /**
     * OAuth Api key
     * @var string
     */
    protected $apiKey;

    /**
     * OAuth secret
     * @var string
     */
    protected $secret;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Used to determine uploader name & set cache key
     * @var string
     */
    protected $name = 'abstract_uploader';

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->client = new Client();
    }

    /**
     * Checking for this uploader has a valid token
     * @return bool
     */
    public function hasValidToken()
    {
        $expiresName = $this->buildCacheKey(static::TOKEN_EXPIRES_CACHE_NAME);

        return $this->storage->has($expiresName)
            && $this->getTokenExpires() > time();
    }

    /**
     * Save token data to storage
     * @param string $token
     * @param $token_type
     * @param int $expires_at
     * @param string $refresh_token
     * @return $this
     */
    public function saveToken($token, $token_type, $expires_at, $refresh_token)
    {
        $this->storage->set($this->buildCacheKey(static::TOKEN_ACCESS_KEY_CACHE_NAME), $token);
        $this->storage->set($this->buildCacheKey(static::TOKEN_REFRESH_CACHE_NAME), $refresh_token);
        $this->storage->set($this->buildCacheKey(static::TOKEN_EXPIRES_CACHE_NAME), $expires_at);
        $this->storage->set($this->buildCacheKey(static::TOKEN_TYPE_CACHE_NAME), $token_type);

        return $this;
    }

    /**
     * Get open auth access token
     * @return mixed|null
     */
    public function getTokenAccessKey()
    {
        // Checking for expires & refresh
        $expireName = $this->buildCacheKey(static::TOKEN_EXPIRES_CACHE_NAME);
        if ($this->storage->has($expireName) && (int)$this->storage->get($expireName) < time()) {
            $this->refreshToken();
        }

        if (!$this->hasValidToken()) {
            return null;
        }

        return $this->storage->get($this->buildCacheKey(static::TOKEN_ACCESS_KEY_CACHE_NAME));
    }

    /**
     * Get token expire time, return now if not exist
     * @return int
     */
    public function getTokenExpires()
    {
        $name = $this->buildCacheKey(static::TOKEN_EXPIRES_CACHE_NAME);
        if (!$this->storage->has($name)) {
            return time();
        }
        return (int) $this->storage->get($name);
    }

    public function getRefreshToken()
    {
        $name = $this->buildCacheKey(static::TOKEN_REFRESH_CACHE_NAME);
        return $this->storage->get($name);
    }

    /**
     * Get token type field
     * @return string
     */
    public function getTokenType()
    {
        return $this->storage->get($this->buildCacheKey(static::TOKEN_TYPE_CACHE_NAME));
    }

    /**
     * Clear all token data
     */
    public function clearTokenData()
    {
        $this->storage->delete($this->buildCacheKey(static::TOKEN_EXPIRES_CACHE_NAME));
        $this->storage->delete($this->buildCacheKey(static::TOKEN_REFRESH_CACHE_NAME));
        $this->storage->delete($this->buildCacheKey(static::TOKEN_ACCESS_KEY_CACHE_NAME));
    }

    /**
     * Get plugin prefix for management storage token, timeout
     *
     * @return string
     */
    protected function getPluginPrefix()
    {
        return $this->name . '_';
    }

    /**
     * Build cache key with plugin prefix against conflict
     * @param $name
     * @return string
     */
    protected function buildCacheKey($name)
    {
        return $this->getPluginPrefix() . $name;
    }

    /**
     * Set api key
     * @param string $key
     * @return $this
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Set secret
     * @param string $secret
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    protected function checkApiKey()
    {
        if (empty($this->apiKey) || empty($this->secret)) {
            throw new ApiKeyNotSuppliedException;
        }
    }
}
