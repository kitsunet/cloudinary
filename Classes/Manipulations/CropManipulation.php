<?php
namespace Kitsunet\Cloudinary\Manipulations;

use Kitsunet\Cloudinary\CloudinaryImageBlob;
use Kitsunet\ImageManipulation\ImageBlob\BoxInterface;
use Kitsunet\ImageManipulation\ImageBlob\ImageBlobInterface;
use Kitsunet\ImageManipulation\ImageBlob\Manipulation\ImageManipulationInterface;
use Kitsunet\ImageManipulation\ImageBlob\Point;

/**
 *
 */
class CropManipulation implements ImageManipulationInterface
{
    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * CropManipulation constructor.
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     */
    public function __construct(int $x, int $y, int $width, int $height)
    {
        $this->x = $x;
        $this->y = $y;
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
            "x" => $this->x,
            "y" => $this->y,
            "crop" => "crop"
        ]);

        return new CloudinaryImageBlob($cloudinaryManipulated, $image->getMetadata());
    }

    /**
     * @param array $options
     * @return static
     */
    public static function fromOptions(array $options)
    {
        return new static((int)$options['x'], (int)$options['y'], (int)$options['width'], (int)$options['height']);
    }

    /**
     * @param Point $point
     * @param BoxInterface $box
     * @return static
     */
    public static function fromPointAndBox(Point $point, BoxInterface $box)
    {
        return new static($point->getX(), $point->getY(), $box->getWidth(), $box->getHeight());
    }

}
