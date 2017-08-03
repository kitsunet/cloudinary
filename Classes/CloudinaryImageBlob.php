<?php
namespace Kitsunet\Cloudinary;

use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\Box;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;

/**
 *
 */
class CloudinaryImageBlob implements ImageBlobInterface
{
    /**
     * @var ManipulatedImage
     */
    protected $manipulatedImage;

    /**
     * @var BlobMetadata
     */
    protected $blobMetadata;

    /**
     * CloudinaryImageBlob constructor.
     *
     * @param ManipulatedImage $manipulatedImage
     * @param BlobMetadata $blobMetadata
     */
    public function __construct(ManipulatedImage $manipulatedImage, BlobMetadata $blobMetadata)
    {
        $this->manipulatedImage = $manipulatedImage;
        $this->blobMetadata = $blobMetadata;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->manipulatedImage->getStream();
    }

    /**
     * @return BlobMetadata
     */
    public function getMetadata(): BlobMetadata
    {
        return $this->blobMetadata;
    }

    /**
     * @param resource $stream
     * @param BlobMetadata $blobMetadata
     * @return \Kitsunet\ImageManipulation\Blob\BlobInterface|ImageBlobInterface
     */
    public static function fromStream($stream, BlobMetadata $blobMetadata)
    {
        die('CREATED FROM STREAM');
        $factory = new ImageBlobFactory();
        return $factory->create($stream, $blobMetadata);
    }

    /**
     * @return BoxInterface
     */
    public function getSize(): BoxInterface
    {
        // TODO This is not always correct...
        return new Box($this->manipulatedImage->getCloudinaryImage()->getWidth(), $this->manipulatedImage->getCloudinaryImage()->getHeight());
    }

    /**
     * @return ManipulatedImage
     */
    public function getManipulatedImage(): ManipulatedImage
    {
        return $this->manipulatedImage;
    }
}
