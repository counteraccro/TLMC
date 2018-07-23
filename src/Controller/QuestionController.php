<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;

class QuestionController extends AppController
{
    /**
     * @Route("/question", name="question")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Question::class);
        
        $questions = $repository->findAll();
        
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
            'questions' => $questions
        ]);
    }
}
