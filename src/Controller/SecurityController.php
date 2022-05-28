<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment as Twig_Environment;

class SecurityController extends AbstractController
{
    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return new Response($this->twig->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' =>  $error
        ])
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    }
}
