<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomepageController
 * @package App\Controller
 */
class HomepageController extends AbstractController
{
    /**
     * Homepage
     * @Route(path="/", name="homepage", methods={"GET"})
     */
    public function index()
    {
        return $this->render('homepage/index.html.twig', [
        ]);
    }
}
