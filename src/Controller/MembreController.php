<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Membre;

class MembreController extends AppController
{
    /**
     * @Route("/membre", name="membre")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Membre::class);
        
        // look for a single Product by its primary key (usually "id")
        $membres = $repository->findAll();
        
        return $this->render('membre/index.html.twig', [
            'controller_name' => 'MembreController',
            'membres' => $membres
        ]);
    }
}
