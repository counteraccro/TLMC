<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Specialite;

class SpecialiteController extends AppController
{
    /**
     * @Route("/specialite", name="specialite")
     */
    public function index()
    {
        
        $repository = $this->getDoctrine()->getRepository(Specialite::class);
        
        $specialites = $repository->findAll();
        
        return $this->render('specialite/index.html.twig', [
            'controller_name' => 'SpecialiteController',
            'specialites' => $specialites
        ]);
    }
}
