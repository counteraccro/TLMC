<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Questionnaire;

class QuestionnaireController extends AppController
{
    /**
     * @Route("/questionnaire", name="questionnaire")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Questionnaire::class);
        
        $questionnaires = $repository->findAll();
        
        return $this->render('questionnaire/index.html.twig', [
            'controller_name' => 'QuestionnaireController',
            'questionnaires' => $questionnaires
        ]);
    }
}
