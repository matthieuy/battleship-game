<?php

namespace App\Utils;

use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImageManipulator
 */
class ImageManipulator
{
    private $sourcePath;
    private $img;

    /**
     * ImageManipulator constructor.
     * @param string $sourcePath
     */
    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
        $this->img = imagecreatefrompng($this->sourcePath);
    }

    /**
     * Change color
     * @param array<int> $rgbSource
     * @param string     $hexColor
     *
     * @return $this
     */
    public function changeColor(array $rgbSource, string $hexColor): self
    {
        // Destination color
        $red = hexdec(substr($hexColor, 0, 2));
        $green = hexdec(substr($hexColor, 2, 2));
        $blue = hexdec(substr($hexColor, 4, 2));

        // Change color
        $white = imagecolorclosest($this->img, $rgbSource[0], $rgbSource[1], $rgbSource[2]);
        imagecolorset($this->img, $white, $red, $green, $blue);

        return $this;
    }

    /**
     * Resize image
     * @param int      $width
     * @param int|null $height
     * @param bool     $keepRation
     *
     * @return $this
     */
    public function resize(int $width, ?int $height = null, bool $keepRation = true): self
    {
        // Ration
        $ratioClosure = null;
        if ($keepRation) {
            $ratioClosure = function (Constraint $constraint): void {
                $constraint->aspectRatio();
            };
        }

        $manager = new ImageManager(['driver' => 'gd']);
        $this->img = $manager->make($this->img);
        $this->img
            ->resize($width, $height, $ratioClosure);

        return $this;
    }

    /**
     * Save the image to destpath
     * @param string $destPath
     */
    public function save(string $destPath): void
    {
        $img = $this->img;
        $img
            ->save($destPath)
            ->destroy();
    }

    /**
     * Get binary response
     * @param Request $request
     * @param string  $filePath
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getResponse(Request $request, string $filePath): Response
    {
        // Get content file
        $resource = fopen($filePath, 'rb');
        $content = stream_get_contents($resource);
        fclose($resource);

        // Response
        $response = new Response($content);
        $response->headers->set('Content-Type', 'image/png');
        $response
            ->setEtag(md5($content))
            ->setLastModified(\DateTime::createFromFormat('U', filemtime($filePath)))
            ->isNotModified($request);

        return $response;
    }
}
