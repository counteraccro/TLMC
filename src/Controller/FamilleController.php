<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Famille;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\FamilleType;
use App\Entity\Patient;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Evenement;
use App\Entity\FamilleAdresse;

class FamilleController extends AppController
{

    /**
     * Listing des familles
     *
     * @Route("/famille/listing/{page}/{field}/{order}", name="famille_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param int $page
     * @param string $field
     * @param string $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, SessionInterface $session, int $page = 1, $field = null, $order = null)
    {
        if (is_null($field)) {
            $field = 'id';
        }

        if (is_null($order)) {
            $order = 'DESC';
        }

        $can_add = true;

        $params = array(
            'field' => $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => Famille::class,
            'repository' => 'Famille',
            'repositoryMethode' => 'findAllFamilles'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();

            $id_specialite = (! is_null($membre->getSpecialite()) ? $membre->getSpecialite()->getId() : '0');
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0',
                'patient.specialite = ' . $id_specialite
            );

            $params['jointure'] = array(
                array(
                    'oldrepository' => 'Famille',
                    'newrepository' => 'patient'
                )
            );

            $can_add = (! is_null($membre->getSpecialite()) ? true : false);
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'famille_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('famille/index.html.twig', array(
            'familles' => $result['paginator'],
            'can_add' => $can_add,
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste des familles"
            )
        ));
    }

    /**
     * Bloc famille d'un patient
     *
     * @Route("/famille/ajax/see/{id}/{page}", name="famille_ajax_see")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Patient $patient
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Patient $patient, int $page = 1)
    {
        $params = array(
            'field' => 'nom ASC, Famille.prenom',
            'order' => 'ASC',
            'page' => $page,
            'repositoryClass' => Famille::class,
            'repository' => 'Famille',
            'repositoryMethode' => 'findAllFamilles',
            'jointure' => array(
                array(
                    'oldrepository' => 'Famille',
                    'newrepository' => 'famillePatients'
                )
            )
        );

        $params['condition'] = array(
            'famillePatients.patient = ' . $patient->getId()
        );

        if (! $this->isAdmin()) {
            $params['condition'][] = $params['repository'] . '.disabled = 0';
        }

        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'famille_ajax_see',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#ajax_patient_famille_see',
            'route_params' => array(
                'id' => $patient->getId()
            )
        );

        return $this->render('famille/ajax_see_liste.html.twig', array(
            'patient' => $patient,
            'familles' => $result['paginator'],
            'pagination' => $pagination
        ));
    }

    /**
     * Affichage de la fiche d'une famille
     *
     * @Route("/famille/see/{id}/{page}", name="famille_see")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Famille $famille, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('famille/ajax_see.html.twig', array(
                'famille' => $famille
            ));
        }

        return $this->render('famille/see.html.twig', array(
            'page' => $page,
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Fiche d'une famille"
            )
        ));
    }

    /**
     * Ajout d'une famille
     *
     * @Route("/famille/add/{page}", name="famille_add")
     * @Route("/famille/ajax/add/{id}", name="famille_ajax_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $famille = new Famille();

        $repository = $this->getDoctrine()->getRepository(Patient::class);

        if ($request->isXmlHttpRequest()) {

            $patient = $repository->findOneBy(array(
                'id' => $id
            ));
            $famille->setPatient($patient);

            $form = $this->createForm(FamilleType::class, $famille, array(
                'label_submit' => 'Ajouter',
                'disabled_patient' => true,
                'label_adresse' => ' ',
                'allow_extra_fields' => true
            ));
        } else {

            $form = $this->createForm(FamilleType::class, $famille, array(
                'label_submit' => 'Ajouter',
                'query_patient' => $repository->getPatientForOneSpecialite($this->getMembre()
                    ->getSpecialite(), $this->isAdmin())
            ));
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $famille->setDisabled(0);
            $famille->getFamilleAdresse()->setDisabled(0);

            $em = $this->getDoctrine()->getManager();

            // vérification pour éviter les doublons dans la table participant
            $adresse = $this->getDoctrine()
                ->getRepository(FamilleAdresse::class)
                ->findOneBy(array(
                'numero_voie' => $famille->getFamilleAdresse()
                    ->getNumeroVoie(),
                'voie' => $famille->getFamilleAdresse()
                    ->getVoie(),
                'ville' => $famille->getFamilleAdresse()
                    ->getVille(),
                'code_postal' => $famille->getFamilleAdresse()
                    ->getCodePostal()
            ));

            if (! is_null($adresse)) {
                $famille->setFamilleAdresse($adresse);
            }

            $em->persist($famille);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('famille_see', array(
                    'id' => $famille->getId()
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            $encoders = array(
                new XmlEncoder(),
                new JsonEncoder()
            );
            $normalizers = array(
                new ObjectNormalizer()
            );

            $serializer = new Serializer($normalizers, $encoders);

            $jsonPatient = $serializer->normalize($patient, null, array(
                'attributes' => array(
                    'nom',
                    'familles' => array(
                        'id',
                        'nom',
                        'prenom',
                        'familleAdresse' => array(
                            'voie',
                            'numeroVoie',
                            'ville',
                            'codePostal'
                        )
                    )
                )
            ));

            // on récupère la valeur du select adresse qui n'est pas conservé en cas d'erreur
            $resultForm = $request->request->all();
            if (! empty($resultForm)) {
                $select_famille = $resultForm['famille']['adresse_select'];
            } else {
                $select_famille = - 1;
            }

            return $this->render('famille/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'patient' => $patient,
                'jsonPatient' => $jsonPatient,
                'select_famille' => $select_famille
            ));
        }

        return $this->render('famille/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Ajout d'une famille"
            )
        ));
    }

    /**
     * Edition d'une famille
     *
     * @Route("/famille/edit/{id}/{page}", name="famille_edit")
     * @Route("/famille/ajax/edit/{id}/{page}", name="famille_ajax_edit")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Famille $famille, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(FamilleType::class, $famille, array(
            'label_submit' => 'Modifier',
            'disabled_patient' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($famille);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true,
                    'page' => $page
                ));
            }
            return $this->redirect($this->generateUrl('famille_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('famille/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'famille' => $famille,
                'page' => $page
            ));
        }

        return $this->render('famille/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de familles'
                ),
                'active' => 'Edition de #' . $famille->getId() . ' - ' . $famille->getPrenom() . ' ' . $famille->getNom()
            )
        ));
    }

    /**
     * Désactivation d'une famille
     *
     * @Route("/famille/delete/{id}/{page}", name="famille_delete")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Famille $famille, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($famille->getDisabled() == 1) {
            $famille->setDisabled(0);
        } else {
            $famille->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($famille);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('famille_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Mise à jour du dropdown Famille lorsque l'événement change dans le formulaire d'ajout d'un témoignage
     *
     * @Route("/famille/ajax/add/dropdown/{id}", name="famille_ajax_add_dropdown", defaults={"id" = 0})
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Evenement $evenement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxDropdownAction(Evenement $evenement = null)
    {
        $repository = $this->getDoctrine()->getRepository(Famille::class);

        $evenement_id = (is_null($evenement) ? 0 : $evenement->getId());

        if ($this->isAdmin()) {
            $specialite_id = null;
        } elseif (is_null($this->getMembre()->getSpecialite())) {
            $specialite_id = 0;
        } else {
            $specialite_id = $this->getMembre()
                ->getSpecialite()
                ->getId();
        }

        $resultats = $repository->getFamilles($evenement_id, $specialite_id);

        $familles = array();
        foreach ($resultats as $resultat) {
            if (isset($familles[$resultat['id_patient']])) {
                $familles[$resultat['id_patient']][] = $resultat;
            } else {
                $familles[$resultat['id_patient']][0] = $resultat;
            }
        }

        return $this->render('famille/ajax_dropdown.html.twig', array(
            'familles' => $familles
        ));
    }

    /**
     * Mise à jour de la liste de familles d'un patient pour l'ajout d'un historique
     *
     * @Route("/famille/ajax/liste/{id}", name="famille_ajax_liste", defaults={"id" = 0})
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Patient $patient
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxListeAction(Patient $patient = null)
    {
        if (is_null($patient)) {
            $familles = array();
        } else {
            $familles = $patient->getFamilles();
        }

        return $this->render('famille/ajax_liste.html.twig', array(
            'familles' => $familles
        ));
    }
}
