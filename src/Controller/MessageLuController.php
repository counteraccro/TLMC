<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\MessageLu;

class MessageLuController extends AppController
{
    /**
     * @Route("/messagelu", name="messagelu")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(MessageLu::class);
        
        $messageLus = $repository->findAll();
        
        return $this->render('message_lu/index.html.twig', [
            'controller_name' => 'MessageLuController',
            'messageLus' => $messageLus
        ]);
    }
}
