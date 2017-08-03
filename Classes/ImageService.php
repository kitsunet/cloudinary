<?php
namespace Kitsunet\Cloudinary;

use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\EmptyBox;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlob;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageServiceInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\PersistentResourceHelper;
use Kitsunet\ImageManipulation\ImageBlob\ResourceProcessorInterface;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
/**
 * @Flow\Scope("singleton")
 */
class ImageService implements ImageServiceInterface, ResourceProcessorInterface
{
    /**
     * @var PersistentResourceHelper
     */
    protected $persistentResourceHelper;

    /**
     * @var ImageBlobFactory
     */
    protected $imageBlobFactory;

    /**
     * @param ImageBlobFactory $imageBlobFactory
     */
    public function injectImageBlobFactory(ImageBlobFactory $imageBlobFactory)
    {
        $this->imageBlobFactory = $imageBlobFactory;
    }

    /**
     * @param PersistentResourceHelper $persistentResourceHelper
     */
    public function injectPersistentResourceHelper(PersistentResourceHelper $persistentResourceHelper)
    {
        $this->persistentResourceHelper = $persistentResourceHelper;
    }

    /**
     * @param ImageBlobInterface $blob
     * @param array $manipulations
     * @return ImageBlobInterface
     */
    public function manipulate(ImageBlobInterface $blob, array $manipulations)
    {
        // TODO: Special handling for SVG should be refactored at a later point.
        if ($blob->getMetadata()->getMediaType() === 'image/svg+xml') {
            return $blob;
        }

        $manipulations = $this->persistentResourceHelper->wrapManipulations($blob, $manipulations);
        $blob = $manipulations->applyTo($blob);
        return $blob;
    }

    /**
     * @param PersistentResource $originalResource
     * @param ImageManipulationInterface[] $manipulations
     * @return array
     */
    public function processResource(PersistentResource $originalResource, array $manipulations)
    {
        $blobMetadata = $this->persistentResourceHelper->prepareMetadata($originalResource);

        $blobMetadata = $blobMetadata->withOptions(Arrays::arrayMergeRecursiveOverrule($blobMetadata->getOptions(), ['cloudinary' => ['originalIdentifier' =>  $originalResource->getSha1()]]));
        $blob = $this->imageBlobFactory->create($originalResource->getStream(), $blobMetadata);

        $blob = $this->manipulate($blob, $manipulations);
        $newResource = $this->persistentResourceHelper->storeImageBlob($blob, $originalResource->getCollectionName());
        // TODO: Shortcut to get the size at the end...
        $blob = new ImageBlob($newResource->getStream(), $blob->getMetadata());

        return $this->prepareReturnValue($newResource, $blob->getSize());
    }

    /**
     * Prepare an array to return.
     *
     * @param PersistentResource $resource
     * @param BoxInterface $imageSize
     * @return array
     */
    protected function prepareReturnValue(PersistentResource $resource, BoxInterface $imageSize)
    {
        return [
            'width' => $imageSize->getWidth(),
            'height' => $imageSize->getHeight(),
            'resource' => $resource
        ];
    }
}
