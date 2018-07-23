<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;

class MessageController extends AppController
{
    /**
     * @Route("/message", name="message")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);
        
        $messages = $repository->findAll();
        
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
            'messages' => $messages
        ]);
    }
}
