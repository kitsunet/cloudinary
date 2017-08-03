<?php
namespace Kitsunet\Cloudinary;

/**
 *
 */
class ManipulatedImage
{
    /**
     * @var CloudinaryImage
     */
    protected $cloudinaryImage;

    /**
     * Array of cloudinary API manipulations (each entry an array of options)
     *
     * @var array
     */
    protected $manipulations;

    public function __construct(CloudinaryImage $cloudinaryImage, array $manipulations = [])
    {
        $this->cloudinaryImage = $cloudinaryImage;
        $this->manipulations = $manipulations;
    }

    /**
     * @return CloudinaryImage
     */
    public function getCloudinaryImage(): CloudinaryImage
    {
        return $this->cloudinaryImage;
    }

    /**
     * @return array
     */
    public function getManipulations(): array
    {
        return $this->manipulations;
    }

    /**
     * Returns a new manipulated image with the given array of options for a single manipulation added.
     *
     * @param array $manipulation
     * @return ManipulatedImage
     */
    public function withManipulation(array $manipulation)
    {
        $newManipulations = $this->manipulations;
        $newManipulations[] = $manipulation;
        return new static($this->cloudinaryImage, $newManipulations);
    }

    /**
     * @param array $options
     * @return mixed|null|string
     */
    public function getUrl(array $options = [])
    {
        $options['transformation'] = $this->manipulations;
        return cloudinary_url($this->cloudinaryImage->getPublicIdentifier(), $options);
    }

    /**
     * @param array $options
     * @return bool|resource
     */
    public function getStream($options = [])
    {
        return fopen($this->getUrl($options), 'r');
    }
}
