<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ExtensionFormulaire;

class ExtensionFormulaireController extends AppController
{
    /**
     * @Route("/extension", name="extension")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(ExtensionFormulaire::class);
        
        // look for a single Product by its primary key (usually "id")
        $extensionFormulaires = $repository->findAll();
        
        return $this->render('extension_formulaire/index.html.twig', [
            'controller_name' => 'ExtensionFormulaireController',
            'extensionFormulaires' => $extensionFormulaires
        ]);
    }
}
