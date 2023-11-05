<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/legal', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('home/legal.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    
     #[Route("/change-language/{newLanguage}", name:"change_language")]
     
     public function changeLanguage(Request $request, SessionInterface $session, $newLanguage): Response
     {
        $session->set('_locale', $newLanguage);
    
        $referer = $request->headers->get('referer');  // Récupérez l'URL du Referer
    
        if($referer) {
            return $this->redirect($referer);  // Redirigez vers la page d'origine
        }
    
        return $this->redirectToRoute('app_home');  // Par défaut, redirigez vers la page d'accueil
    }
}
