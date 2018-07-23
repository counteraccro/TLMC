<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reponse;

class ReponseController extends AppController
{
    /**
     * @Route("/reponse", name="reponse")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Reponse::class);
        
        $reponses = $repository->findAll();
        
        return $this->render('reponse/index.html.twig', [
            'controller_name' => 'ReponseController',
            'reponses' => $reponses
        ]);
    }
}
