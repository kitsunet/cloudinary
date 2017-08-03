<?php
namespace Kitsunet\Cloudinary;

use Cloudinary\Api;
use Cloudinary\Uploader;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Http\Request;

/**
 * @Flow\Scope("singleton")
 */
class CloudinaryProvider
{
    /**
     * @Flow\InjectConfiguration(path="cloud_name")
     * @var string
     */
    protected $cloudName;

    /**
     * @Flow\InjectConfiguration(path="credentials")
     * @var array
     */
    protected $credentials;

    /**
     * @var CurlEngine
     */
    protected $curlEngine;

    /**
     *
     */
    public function initializeObject()
    {
        \Cloudinary::config(array_merge(['cloud_name' => $this->cloudName], $this->credentials));
        unset($this->credentials);
    }

    /**
     * @param string $file
     * @param string $publicIdentifier
     * @param array $options
     * @return CloudinaryImage
     */
    public function upload($file, $publicIdentifier, array $options = [])
    {
        $options['public_id'] = $publicIdentifier;
        $uploadResult = Uploader::upload($file, $options);
        return new CloudinaryImage((array)$uploadResult);
    }

    /**
     * @param string $publicIdentifier
     * @return CloudinaryImage|null
     */
    public function find(string $publicIdentifier)
    {
        $api = new Api();
        try {
            $rawData = $api->resource($publicIdentifier);
            return new CloudinaryImage((array)$rawData);
        } catch (\Exception $e) {
            return null;
        }
    }
}
