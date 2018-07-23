<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Etablissement;

class EtablissementController extends AppController
{
    /**
     * @Route("/etablissement", name="etablissement")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Etablissement::class);
        
        $etablissements = $repository->findAll();
        
        return $this->render('etablissement/index.html.twig', [
            'controller_name' => 'EtablissementController',
            'etablissements' => $etablissements
        ]);
    }
}
