<?php
namespace Kitsunet\Cloudinary\Manipulations;

use Kitsunet\Cloudinary\CloudinaryImageBlob;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;

/**
 *
 */
class ResizeManipulation implements ImageManipulationInterface
{

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;


    /**
     * ResizeManipulation constructor.
     *
     * @param int $width
     * @param int $height
     */
    protected function __construct(int $width = 0, int $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param ImageBlobInterface $image
     * @return ImageBlobInterface
     */
    public function applyTo(ImageBlobInterface $image): ImageBlobInterface
    {
        // TODO Upgrade
        if (!$image instanceof CloudinaryImageBlob) {
            return $image;
        }

        $cloudinaryManipulated = $image->getManipulatedImage();
        $cloudinaryManipulated = $cloudinaryManipulated->withManipulation([
            "width" => $this->width,
            "height" => $this->height,
            "crop" => "limit"
        ]);

        return new CloudinaryImageBlob($cloudinaryManipulated, $image->getMetadata());
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        return new static((int)$options['width'], (int)$options['height']);
    }

    /**
     * @param BoxInterface $box
     * @return static
     */
    public static function toBox(BoxInterface $box): self
    {
        return new static($box->getWidth(), $box->getHeight());
    }
}
