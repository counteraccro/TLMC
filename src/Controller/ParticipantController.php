<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participant;

class ParticipantController extends AppController
{
    /**
     * @Route("/participant", name="participant")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Participant::class);
        
        $participants = $repository->findAll();
        
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
            'participants' => $participants
        ]);
    }
}
