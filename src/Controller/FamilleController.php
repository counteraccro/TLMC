<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Famille;

class FamilleController extends AppController
{
    /**
     * @Route("/famille", name="famille")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Famille::class);
        
        // look for a single Product by its primary key (usually "id")
        $familles = $repository->findAll();
        
        return $this->render('famille/index.html.twig', [
            'controller_name' => 'FamilleController',
            'familles' => $familles
        ]);
    }
}
