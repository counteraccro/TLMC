<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TypeProduitController extends Controller
{
    /**
     * @Route("/type/produit", name="type_produit")
     */
    public function index()
    {
        return $this->render('type_produit/index.html.twig', [
            'controller_name' => 'TypeProduitController',
        ]);
    }
}
