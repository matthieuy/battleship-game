<?php

namespace App\Controller;

use App\Entity\Game;
use App\Utils\ImageManipulator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BoatController
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BoatController extends AbstractController
{
    private $rootDir;
    private $destDir;

    /**
     * BoatController constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
        $this->destDir = realpath($this->rootDir.'/var').'/boats';
    }

    /**
     * Dynamic CSS
     * @Route(
     *     name="match.css",
     *     path="/game/{slug}.css",
     *     methods={"GET"},
     *     requirements={"slug": "([0-9A-Za-z\-]+)"})
     *
     * @param Game    $game
     * @param Request $request
     *
     * @return Response
     */
    public function css(Game $game, Request $request): Response
    {
        // Default value
        $isMobile = $this->isMobile($request);
        $boxSize = 20;
        $displayGrid = $isMobile;

        // View (CSS content)
        $response = $this->render('match/game.css.twig', [
            'boxSize' => $boxSize,
            'size' => $boxSize * $game->getSize(),
            'game' => $game,
            'isMobile' => $isMobile,
            'borders' => range(0, $game->getSize(), 10), // Border for mobile
            'displayGrid' => $displayGrid,
        ]);
        $response->headers->set('Content-Type', 'text/css');

        // Compress
        $content = $response->getContent();
        $content = str_replace(["    ", "\t", "\r", "\n"], '', $content);
        $response->setContent($content);

        return $response;
    }

    /**
     * Get the boat image
     * @Route(
     *     name="match.boat.img",
     *     path="/img/boats/{color}-{size}.png",
     *     methods={"GET"},
     *     requirements={"color": "([0-9A-F]{6})", "size": "([2-6]0)"},
     *     defaults={"size": "60"})
     *
     * @param Request $request
     * @param string  $color
     * @param int     $size
     *
     * @return Response
     */
    public function boatImage(Request $request, string $color, int $size = 60): Response
    {
        $destPath = "$this->destDir/$color-$size.png";

        if (!file_exists($destPath)) {
            if (!file_exists($this->destDir)) {
                mkdir($this->destDir);
            }

            // Create image
            $image = new ImageManipulator(realpath($this->rootDir.'/public/img/boat.png'));
            $image
                ->changeColor([255, 255, 255], $color)
                ->resize($size * 8, $size * 7, false)
                ->save($destPath);
        }

        // Return binary response
        return ImageManipulator::getResponse($request, $destPath);
    }

    /**
     * Get the rocket image
     * @Route(
     *     name="match.rocket.img",
     *     path="/img/rocket/{color}-{size}.png",
     *     methods={"GET"},
     *     requirements={"color": "([0-9A-F]{6})", "size": "([2-6]0)"},
     *     defaults={"size": "60"})
     *
     * @param Request $request
     * @param string  $color
     * @param int     $size
     *
     * @return Response
     */
    public function rocketImage(Request $request, string $color, int $size = 60): Response
    {
        $destPath = "$this->destDir/rocket-$color-$size.png";

        if (!file_exists($destPath)) {
            if (!file_exists($this->destDir)) {
                mkdir($this->destDir);
            }

            // Create image
            $image = new ImageManipulator(realpath($this->rootDir.'/public/img/rocket.png'));
            $image
                ->changeColor([255, 0, 0], $color)
                ->resize($size / 2, null, true)
                ->save($destPath);
        }

        // Return binary response
        return ImageManipulator::getResponse($request, $destPath);
    }

    /**
     * Get the explose image
     * @Route(
     *     name="match.explose.img",
     *     path="/img/explose-{size}.png",
     *     methods={"GET"},
     *     requirements={"size": "([2-6]0)"},
     *     defaults={"size": "60"})
     *
     * @param Request $request
     * @param int     $size
     *
     * @return Response
     */
    public function exploseImage(Request $request, int $size = 60): Response
    {
        $destPath = "$this->destDir/explose-$size.png";

        if (!file_exists($destPath)) {
            if (!file_exists($this->destDir)) {
                mkdir($this->destDir);
            }

            // Create image
            $image = new ImageManipulator(realpath($this->rootDir.'/public/img/explose.png'));
            $image
                ->resize($size * 12, $size * 2, false)
                ->save($destPath);
        }

        // Return binary response
        return ImageManipulator::getResponse($request, $destPath);
    }

    /**
     * Check user-agent for mobile device
     * @param Request $request
     *
     * @return bool
     */
    private function isMobile(Request $request): bool
    {
        $userAgent = $request->headers->get('user-agent');
        $regex = "/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|
            hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|
            opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|
            treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i";

        return preg_match($regex, $userAgent) > 0;
    }
}
