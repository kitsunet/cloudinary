<?php
namespace Kitsunet\Cloudinary;

use Neos\Flow\Annotations as Flow;
use Kitsunet\ImageManipulation\Blob\BlobMetadata;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobFactoryInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Neos\Flow\Utility\Algorithms;
use Neos\Utility\MediaTypes;
use Neos\Utility\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class ImageBlobFactory implements ImageBlobFactoryInterface
{
    /**
     * @Flow\Inject
     * @var CloudinaryProvider
     */
    protected $cloudinaryProvider;

    /**
     * @param resource $stream
     * @param BlobMetadata $blobMetadata
     * @return ImageBlobInterface
     */
    public function create($stream, BlobMetadata $blobMetadata): ImageBlobInterface
    {
        $cloudinaryOptions = $blobMetadata->getOptionsInNamespace('cloudinary');
        $identifier = ObjectAccess::getPropertyPath($cloudinaryOptions, 'originalIdentifier');

        if (!empty($identifier)) {
            $cloudinaryImage = $this->cloudinaryProvider->find($identifier);
        }

        if (empty($cloudinaryImage)) {
            var_dump('NOT FOUND ' . $identifier);
            $identifier = $identifier ?? Algorithms::generateUUID();
            $extension = MediaTypes::getFilenameExtensionFromMediaType($blobMetadata->getMediaType());
            $cloudinaryImage = $this->uploadFile($stream, $identifier, $extension, []);
        }

        $manipulatedImage = new ManipulatedImage($cloudinaryImage, []);
        return new CloudinaryImageBlob($manipulatedImage, $blobMetadata);
    }

    /**
     * @param resource $stream
     * @param string $identifier
     * @param string $extension
     * @param array $options
     * @return CloudinaryImage
     */
    protected function uploadFile($stream, $identifier, $extension, $options = [])
    {
        // TODO: This could be way more performant if the SDK supported streams with CURL. Probably replace SDK with own API connector...
        $temporaryFilename = FLOW_PATH_TEMPORARY . 'imageblob_temporary_' . getmypid() . '_' . $identifier . '.' . $extension;
        $temporaryFile = fopen($temporaryFilename, 'w');
        stream_copy_to_stream($stream, $temporaryFile);
        fclose($temporaryFile);
        $cloudinaryImage = $this->cloudinaryProvider->upload($temporaryFilename, $identifier, $options);
        unlink($temporaryFilename);
        return $cloudinaryImage;
    }
}
