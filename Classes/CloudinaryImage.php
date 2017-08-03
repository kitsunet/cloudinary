<?php
namespace Kitsunet\Cloudinary;

use Neos\Cache\CacheAwareInterface;

/**
 *
 */
class CloudinaryImage implements CacheAwareInterface
{
    /**
     * @var array
     */
    protected $rawData;

    /**
     * CloudinaryImage constructor.
     *
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return string
     */
    public function getPublicIdentifier()
    {
        return $this->rawData['public_id'];
    }

    /**
     * @return string
     */
    public function getCacheEntryIdentifier()
    {
        return $this->getPublicIdentifier();
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->rawData['width'];
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->rawData['height'];
    }

    public function getFormat()
    {
        return $this->rawData['format'];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->rawData['url'];
    }

    /**
     * @return string
     */
    public function getSecureUrl()
    {
        return $this->rawData['secure_url'];
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
