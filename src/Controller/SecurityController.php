<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AppController
{
    /**
     * La route pour se connecter.
     * 
     * @Route("/login", name="security_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        //$this->pre($authenticationUtils);
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
    
    /**
     * La route pour se deconnecter.
     *
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
