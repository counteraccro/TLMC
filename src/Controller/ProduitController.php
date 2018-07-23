<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;

class ProduitController extends AppController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        
        // look for a single Product by its primary key (usually "id")
        $produits = $repository->findAll();
        
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produits
        ]);
    }
}
