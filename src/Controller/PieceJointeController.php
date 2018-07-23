<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PieceJointe;

class PieceJointeController extends AppController
{
    /**
     * @Route("/piecejointe", name="piecejointe")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(PieceJointe::class);
        
        $piecejointes = $repository->findAll();
        
        return $this->render('piece_jointe/index.html.twig', [
            'controller_name' => 'PieceJointeController',
            'piecejointes' => $piecejointes
        ]);
    }
}
