<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends AppController
{

    /**
     *
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

    /**
     * Edition d'une question
     *
     * @Route("/question/edit/{id}/{page}", name="question_edit")
     * @Route("/question/ajax/edit/{id}", name="question_ajax_edit")
     * @ParamConverter("question", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(SessionInterface $session, Request $request, Question $question, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(QuestionType::class, $question, array(
                'ajax_button' => true,
                'attr' => array(
                    'id' => 'question_ajax_edit'
                )
            ));
        } else {
            $form = $this->createForm(QuestionType::class, $question);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($question);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('questionnaire_listing', array(
                    'page' => $page,
                    'field' => $arrayFilters['field'],
                    'order' => $arrayFilters['order']
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('question/ajax_edit.html.twig', [
                'question' => $question,
                'form' => $form->createView(),
                'id' => $question->getId(),
                'liste_type' => AppController::QUESTION_TYPE
            ]);
        }

        return $this->render('question/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'question' => $question,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('questionnaire_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de questions'
                ),
                'active' => 'Edition de #' . $question->getId() . ' - ' . $question->getLibelle()
            )
        ]);
    }
}