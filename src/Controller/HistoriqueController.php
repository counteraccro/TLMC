<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Historique;

class HistoriqueController extends AppController
{
    /**
     * @Route("/historique", name="historique")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Historique::class);
        
        $historiques = $repository->findAll();
        
        return $this->render('historique/index.html.twig', [
            'controller_name' => 'HistoriqueController',
            'historiques' => $historiques
        ]);
    }
}
