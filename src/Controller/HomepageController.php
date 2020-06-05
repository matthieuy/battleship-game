<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class HomepageController
 */
class HomepageController extends AbstractController
{
    /**
     * Homepage
     * @Route(
     *     path="/",
     *     name="homepage",
     *     methods={"GET"},
     *     options={"expose"="true"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
        ]);
    }

    /**
     * Manifest.json
     * @Route(
     *     path="/manifest.json",
     *     name="manifest",
     *     methods={"GET"})
     *
     * @param Translator $translator
     *
     * @return JsonResponse
     */
    public function manifestFile(Translator $translator): JsonResponse
    {
        $manifest = [
           'background_color' => '#FFFFFF',
           'categories' => 'games',
           'display' => 'fullscreen',
           'icons' => [
               [
                   'src' => './img/icons/logo48.png',
                   'sizes' => '48x48',
                   'type' => 'image/png',
               ],
               [
                   'src' => './img/icons/logo96.png',
                   'sizes' => '96x96',
                   'type' => 'image/png',
               ],
               [
                   'src' => './img/icons/logo192.png',
                   'sizes' => '192x192',
                   'type' => 'image/png',
               ],
               [
                   'src' => './img/icons/logo512.png',
                   'sizes' => '512x512',
                   'type' => 'image/png',
               ],
           ],
           'name' => $translator->trans('app_name', [], 'messages'),
           'short_name' => $translator->trans('app_name', [], 'messages'),
           'start_url' => $this->generateUrl('homepage', [], RouterInterface::ABSOLUTE_URL),
           'theme_color' => '#000000',
        ];

        return new JsonResponse($manifest);
    }
}
