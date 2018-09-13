<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TypeEvenementController extends Controller
{
    /**
     * @Route("/type/evenement", name="type_evenement")
     */
    public function index()
    {
        return $this->render('type_evenement/index.html.twig', [
            'controller_name' => 'TypeEvenementController',
        ]);
    }
}
