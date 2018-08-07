<?php
namespace App\Controller;

use App\Entity\Questionnaire;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Form\QuestionnaireType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Reponse;

class QuestionnaireController extends AppController
{

    /**
     * Listing des questionnaires
     *
     * @Route("/questionnaire/listing/{page}/{field}/{order}", name="questionnaire_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     */
    public function index(Request $request, SessionInterface $session, int $page = 1, $field = null, $order = null)
    {
        if (is_null($field)) {
            $field = 'id';
        }

        if (is_null($order)) {
            $order = 'DESC';
        }

        $params = array(
            'field' => $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => Questionnaire::class,
            'repository' => 'Questionnaire',
            'repositoryMethode' => 'findAllQuestionnaires'
        );
        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'questionnaire_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('questionnaire/index.html.twig', [
            'controller_name' => 'QuestionnaireController',
            'questionnaires' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des questionnaires'
            )
        ]);
    }

    /**
     * Fiche d'un questionnaire
     *
     * @Route("/questionnaire/see/{id}/{page}", name="questionnaire_see")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function seeAction(SessionInterface $session, Questionnaire $questionnaire, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('questionnaire/see.html.twig', [
            'page' => $page,
            'questionnaire' => $questionnaire,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('questionnaire_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de questionnaires'
                ),
                'active' => 'Fiche d\'un questionnaire'
            )
        ]);
    }

    /**
     * Fiche d'un questionnaire
     *
     * @Route("/questionnaire/ajax/see/{id}", name="questionnaire_ajax_see")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxSeeAction(Questionnaire $questionnaire)
    {        
        return $this->render('questionnaire/ajax_see.html.twig', [
            'questionnaire' => $questionnaire
        ]);
    }

    /**
     * Ajout d'un nouveau questionnaire
     *
     * @Route("/questionnaire/add/{page}", name="questionnaire_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $questionnaire = new Questionnaire();

        $form = $this->createForm(QuestionnaireType::class, $questionnaire, array('isAdd' => true));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaire->setDisabled(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($questionnaire);
            $em->flush();

            return $this->redirect($this->generateUrl('questionnaire_listing'));
        }

        return $this->render('questionnaire/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('questionnaire_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de questionnaires'
                ),
                'active' => "Ajout d'un questionnaire"
            )
        ]);
    }

    /**
     * Edition d'un questionnaire
     *
     * @Route("/questionnaire/edit/{id}/{page}", name="questionnaire_edit")
     * @Route("/questionnaire/ajax/edit/{id}", name="questionnaire_ajax_edit")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(SessionInterface $session, Request $request, Questionnaire $questionnaire, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(QuestionnaireType::class, $questionnaire, array(
                'attr' => array(
                    'id' => 'questionnaire_ajax_edit'
                )
            ));
        } else {
            $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($questionnaire);
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

            return $this->render('questionnaire/ajax_edit.html.twig', [
                'questionnaire' => $questionnaire,
                'form' => $form->createView(),
                'id' => $questionnaire->getId()
            ]);
        }

        return $this->render('questionnaire/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'questionnaire' => $questionnaire,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('questionnaire_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de questionnaires'
                ),
                'active' => 'Edition de #' . $questionnaire->getId() . ' - ' . $questionnaire->getTitre()
            )
        ]);
    }

    /**
     * desactivation d'un questionnaire
     *
     * @Route("/questionnaire/delete/{id}/{page}", name="questionnaire_delete")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(SessionInterface $session, Questionnaire $questionnaire, $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($questionnaire->getDisabled() == 1) {
            $questionnaire->setDisabled(0);
        } else {
            $questionnaire->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($questionnaire);

        $entityManager->flush();

        return $this->redirectToRoute('questionnaire_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
