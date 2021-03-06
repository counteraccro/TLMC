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
use Symfony\Component\HttpFoundation\Response;
use App\Service\QuestionnaireManager;
use App\Service\ExportManager;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\Question;

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
    public function seeAction(SessionInterface $session, Questionnaire $questionnaire, int $page = 1)
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
     * Fiche d'un questionnaire Ajax
     *
     * @Route("/questionnaire/ajax/see/{id}/{page}", name="questionnaire_ajax_see")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxSeeAction(Questionnaire $questionnaire, $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Questionnaire::class);
        $nb_participants = $repository->getNbParticipantsReponduByQuestionnaire($questionnaire->getId());

        return $this->render('questionnaire/ajax_see.html.twig', [
            'questionnaire' => $questionnaire,
            'statistiques' => array(
                'nb_participants' => $nb_participants[0]['nb_participants']
            ),
            'page' => $page
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

        $form = $this->createForm(QuestionnaireType::class, $questionnaire, array(
            'isAdd' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questionnaire->setDisabled(0);
            $questionnaire->setDateCreation(new \DateTime());
            $questionnaire->setPublication(0);

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

        // Si appel Ajax, on renvoie sur la page ajax
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
    public function deleteAction(Request $request, SessionInterface $session, Questionnaire $questionnaire, $page)
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

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true
            ));
        } else {
            return $this->redirectToRoute('questionnaire_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            ));
        }
    }

    /**
     * Démo questionnaire
     *
     * @Route("/questionnaire/demo/{slug}", name="questionnaire_demo")
     * @ParamConverter("questionnaire", options={"mapping": {"slug": "slug"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function demoAction(Request $request, Questionnaire $questionnaire, QuestionnaireManager $questionnaireManager)
    {
        $questResultat = array(
            'result' => array()
        );
        if ($request->isMethod('POST')) {
            $questResultat = $questionnaireManager->manage($questionnaire, self::DEMO);
        }

        return $this->render('questionnaire/demo.html.twig', [
            'questionnaire' => $questionnaire,
            'questResultat' => $questResultat['result']
        ]);
    }

    /**
     * Prod questionnaire (rendu final pour participants qui vont poster les réponses)
     *
     * @Route("/questionnaire/{slug}", name="questionnaire_prod")
     * @ParamConverter("questionnaire", options={"mapping": {"slug": "slug"}})
     */
    public function questionnaireAction(Request $request, Questionnaire $questionnaire, QuestionnaireManager $questionnaireManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $membre = $this->getMembre();
        $questionnaireManager->allowAccess($questionnaire, $membre);

        $questResultat = array(
            'result' => array(),
            'validateSubmit' => false
        );
        if ($request->isMethod('POST')) {
            $questResultat = $questionnaireManager->manage($questionnaire, self::PROD, $membre);
        }

        return $this->render('questionnaire/questionnaire.html.twig', [
            'questionnaire' => $questionnaireManager->formatDescription($questionnaire, $membre),
            'questResultat' => $questResultat['result'],
            'validateSubmit' => $questResultat['validateSubmit'],
            'membre' => $membre
        ]);
    }

    /**
     * Publication questionnaire
     *
     * @Route("/questionnaire/ajax/publication/{id}", name="questionnaire_ajax_publication")
     * @Route("/questionnaire/ajax/publication/validation/{id}/{val}", name="questionnaire_ajax_publication_validation")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function publicationAction(Questionnaire $questionnaire, $val = '')
    {

        // Cas le questionnaire n'est pas publié et on souhaite le publier
        if ($questionnaire->getPublication() == 0) {
            // Cas confirmation depuis la popin
            if ($val == 1) {

                // Vérification si le questionnaire possède au moins 1 question
                if ($questionnaire->getQuestions()->count() > 0) {
                    // Vérification si le questionnaire possère au moins une question non disabled
                    $is_ok = false;
                    foreach ($questionnaire->getQuestions() as $question) {
                        if (! $question->getDisabled()) {
                            $is_ok = true;
                        }
                    }

                    // Tout est OK
                    if ($is_ok) {

                        $questionnaire->setPublication(1);
                        $questionnaire->setDatePublication(new \DateTime());
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($questionnaire);
                        $entityManager->flush();

                        return $this->json(array(
                            'statut' => true
                        ));
                    } // Erreur Aucune question visible
                    else {
                        return $this->render('questionnaire/ajax_publication.html.twig', [
                            'questionnaire' => $questionnaire,
                            'erreur' => 'Au moins une question dans le questionnaire doit être active'
                        ]);
                    }
                } // Erreur aucune question présente dans le questionnaire
                else {
                    return $this->render('questionnaire/ajax_publication.html.twig', [
                        'questionnaire' => $questionnaire,
                        'erreur' => 'Au moins une question dans le questionnaire doit être présente'
                    ]);
                }
            } // Cas attente de confirmation
            else {

                return $this->render('questionnaire/ajax_publication.html.twig', [
                    'questionnaire' => $questionnaire,
                    'erreur' => ''
                ]);
            }
        } else {

            $questionnaire->setPublication(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($questionnaire);
            $entityManager->flush();

            return $this->json(array(
                'statut' => true
            ));
        }
    }

    /**
     * Statistiques questionnaire
     *
     * @Route("/questionnaire/stats/{id}/{page}/{page_search}", name="questionnaire_stats")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function statsAction(Request $request, Questionnaire $questionnaire, Session $session, int $page = 1, $page_search = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $post = $request->request->all();
        $search = '';
        if (isset($post['search_membre_stats'])) {
            $search = $post['search_membre_stats'];
        }

        $repository = $this->getDoctrine()->getRepository(Membre::class);
        $result = $repository->GetAllMembresReponsesByQuestionnaire($questionnaire->getId(), $page_search, self::MAX_NB_RESULT, $search);

        $pagination = array(
            'page' => $page_search,
            'route' => 'questionnaire_stats',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array(
                'id' => $questionnaire->getId()
            )
        );

        return $this->render('questionnaire/stats.html.twig', [
            'questionnaire' => $questionnaire,
            'liste_membres' => $result['paginator'],
            'pagination' => $pagination,
            'search' => $search,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('questionnaire_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de questionnaires',
                    $this->generateUrl('questionnaire_see', array(
                        'id' => $questionnaire->getId(),
                        'page' => $page
                    )) => 'Fiche du questionnaire'
                ),
                'active' => 'Statistiques de #' . $questionnaire->getId() . ' - ' . $questionnaire->getTitre()
            )
        ]);
    }

    /**
     * Export statistiques questionnaire
     *
     * @Route("/questionnaire/export/{id}/{type_export}", name="questionnaire_export")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function exportAction(Questionnaire $questionnaire, $type_export = 'csv', ExportManager $exportManager)
    {
        // Provide a name for your file with extension
        $date_export = new \DateTime();
        $fileName = 'Questionnaire-' . $questionnaire->getId() . '-' . $questionnaire->getTitre() . '-' . $date_export->format('d-m-Y H:i:s');

        // Dans le cas d'un CSV
        if ($type_export == 'csv') {

            $content = array();

            $repository = $this->getDoctrine()->getRepository(Membre::class);
            $result = $repository->GetAllMembresReponsesByQuestionnaire($questionnaire->getId(), 1, 1000000000, '');

            /* @var Membre $membre */
            foreach ($result['paginator'] as $membre) {
                $content[$membre->getId()]['date'] = '';
                $content[$membre->getId()]['id'] = $membre->getId();
                $content[$membre->getId()]['nom'] = $membre->getNom();
                $content[$membre->getId()]['prénom'] = $membre->getPrenom();

                /* @var Question $question */
                foreach ($questionnaire->getQuestions() as $question) {
                    foreach ($question->getReponses() as $reponse) {
                        if ($reponse->getMembre()->getId() == $membre->getId()) {
                            $content[$membre->getId()]['date'] = $reponse->getDate()->format('d-m-Y H:i:s');

                            $qKey = 'Q#' . $question->getId() . ' ' . $question->getLibelle();

                            $data_value = json_decode($question->getListeValeur());
                            $tmp = explode('|', $reponse->getValeur());

                            if (in_array($question->getType(), array(
                                AppController::CHECKBOXTYPE,
                                AppController::CHOICETYPE,
                                AppController::RADIOTYPE
                            ))) {

                                foreach ($data_value as $val) {
                                    foreach ($tmp as $tmpVal) {
                                        if ($tmpVal == $val->value) {
                                            $content[$membre->getId()]['Q#' . $question->getId() . ' - Valeur'] = $val->value;
                                            $content[$membre->getId()][$qKey] = $val->libelle;
                                        }
                                    }
                                }
                            } else {
                                $content[$membre->getId()][$qKey] = $reponse->getValeur();
                            }
                        }
                    }
                }
            }

            return $exportManager->generateCSV($fileName, $content);

            // Dans le cas d'un XML
        } else if ($type_export == 'xml') {
            $attributes = array(
                'id',
                'titre',
                'description',
                'dateCreation',
                'dateFin',
                'jourRelance',
                'disabled',
                'slug',
                'datePublication',
                'descriptionAfterSubmit',
                'questions' => array(
                    'id',
                    'libelle',
                    'libelleTop',
                    'libelleBottom',
                    'type',
                    'valeurDefaut',
                    'listeValeur',
                    'obligatoire',
                    'regles',
                    'messageErreur',
                    'ordre',
                    'disabled',
                    'reponses' => array(
                        'id',
                        'valeur',
                        'date',
                        'disabled',
                        'membre' => array(
                            'id',
                            'username',
                            'nom',
                            'prenom'
                        )
                    )
                )
            );

            $callback = function ($dateTime) {
                return $dateTime instanceof \DateTime ? $dateTime->getTimestamp() : '';
            };

            $arrayCallback = array(
                'date' => $callback,
                'dateCreation' => $callback,
                'dateFin' => $callback,
                'datePublication' => $callback
            );

            return $exportManager->generateXML($fileName, $questionnaire, $attributes, $arrayCallback);

            // Dans le cas d'un XML-nodata
        } else if ($type_export == 'xml-nodata') {

            $fileName .= '-no-data';

            $attributes = array(
                'id',
                'titre',
                'description',
                'dateCreation',
                'dateFin',
                'jourRelance',
                'disabled',
                'slug',
                'datePublication',
                'descriptionAfterSubmit',
                'questions' => array(
                    'id',
                    'libelle',
                    'libelleTop',
                    'libelleBottom',
                    'type',
                    'valeurDefaut',
                    'listeValeur',
                    'obligatoire',
                    'regles',
                    'messageErreur',
                    'ordre',
                    'disabled'
                )
            );

            $callback = function ($dateTime) {
                return $dateTime instanceof \DateTime ? $dateTime->getTimestamp() : '';
            };

            $arrayCallback = array(
                'dateCreation' => $callback,
                'dateFin' => $callback,
                'datePublication' => $callback
            );

            return $exportManager->generateXML($fileName, $questionnaire, $attributes, $arrayCallback);
        }
    }

    
    /**
     * Duplication questionnaire
     *
     * @Route("/questionnaire/ajax/duplication/{id}/{page}", name="questionnaire_ajax_duplication")
     * @Route("/questionnaire/ajax/duplication/validation/{id}/{statut}/{page}", name="questionnaire_ajax_duplication_validation")
     * @ParamConverter("questionnaire", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param Questionnaire $questionnaire
     * @param number $statut
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function duplicationAction(Request $request, Questionnaire $questionnaire, $statut = 0, $page)
    {
        // Cas confirmation depuis la popin
        if ($statut == 1 && $request->isXmlHttpRequest() === true) {

            $dataPost = $request->request->all();
            $repository = $this->getDoctrine()->getRepository(Questionnaire::class);

            if (! $repository->isExistingTitleAndSlug($dataPost['questionnaire']['titre'], $dataPost['questionnaire']['slug'])) {
                // action duplication

                $entityManager = $this->getDoctrine()->getManager();

                $questionnaireDuplicate = new Questionnaire();
                $questionnaireDuplicate->clone($questionnaire);
                $questionnaireDuplicate->setTitre($dataPost['questionnaire']['titre']);
                $questionnaireDuplicate->setSlug($dataPost['questionnaire']['slug']);
                $questionnaireDuplicate->setPublication(0);

                /* @var Question $question */
                foreach ($questionnaire->getQuestions() as $question) {
                    $questionDuplicate = new Question();
                    $questionDuplicate->clone($question);
                    $questionnaireDuplicate->addQuestion($questionDuplicate);
                    $questionDuplicate->setQuestionnaire($questionnaireDuplicate);
                    $entityManager->persist($questionDuplicate);
                }

                $entityManager->persist($questionnaireDuplicate);
                $entityManager->flush();
                
                return $this->json(array(
                    'statut' => true,
                    'url' => $this->generateUrl('questionnaire_see', array('id' => $questionnaireDuplicate->getId(), 'page' => $page))
                ));
                
            } else {
                return $this->render('questionnaire/ajax_duplication.html.twig', [
                    'questionnaire' => $questionnaire,
                    'erreur' => 'Le titre <b>' . $dataPost['questionnaire']['titre'] . '</b> existe déjà',
                    'page' => $page
                ]);
            }
        }

        return $this->render('questionnaire/ajax_duplication.html.twig', [
            'questionnaire' => $questionnaire,
            'erreur' => '',
            'page' => $page
        ]);
    }
}