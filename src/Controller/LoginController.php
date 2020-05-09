<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends AbstractController
{
    /**
     * Login page
     * @Route(path="/login", name="login", methods={"GET"})
     */
    public function index()
    {
        // Already auth
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/login.html.twig', [
        ]);
    }

    /**
     * Check login form
     * @Route(path="/login", name="login_check", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkLogin()
    {
       return $this->redirectToRoute('homepage');
    }

    /**
     * @Route(path="/logout", name="logout")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout()
    {
        return $this->redirectToRoute('login');
    }
}
