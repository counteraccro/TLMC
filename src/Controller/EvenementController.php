<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;

class EvenementController extends AppController
{
    /**
     * @Route("/evenement", name="evenement")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Evenement::class);
        
        $evenements = $repository->findAll();
        
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
            'evenements' => $evenements
        ]);
    }
}
