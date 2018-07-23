<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Groupe;

class GroupeController extends AppController
{
    /**
     * @Route("/groupe", name="groupe")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Groupe::class);
        
        $groupes = $repository->findAll();
        
        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
            'groupes' => $groupes
        ]);
    }
}
