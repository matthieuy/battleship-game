<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomepageController
 */
class HomepageController extends AbstractController
{
    /**
     * Homepage
     * @Route(path="/", name="homepage", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
        ]);
    }
}
