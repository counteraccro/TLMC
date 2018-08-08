<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpecialiteEvenementController extends Controller
{
    /**
     * @Route("/specialite/evenement", name="specialite_evenement")
     */
    public function index()
    {
        return $this->render('specialite_evenement/index.html.twig', [
            'controller_name' => 'SpecialiteEvenementController',
        ]);
    }
}
