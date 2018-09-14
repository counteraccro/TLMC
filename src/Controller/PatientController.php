<?php
namespace App\Controller;

use App\Entity\Patient;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PatientType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Etablissement;
use App\Entity\Membre;
use App\Entity\Specialite;
use App\Entity\FamilleAdresse;

class PatientController extends AppController
{

    /**
     * Listing des patients.
     * Pour les membres non admins,
     * les patients affichés sont les patients de la spécialité du membre connecté
     *
     * @Route("/patient/listing/{page}/{field}/{order}", name="patient_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Patient::class,
            'repository' => 'Patient',
            'repositoryMethode' => 'findAllPatients'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();
            $id_specialite = (! is_null($membre->getSpecialite()) ? $membre->getSpecialite()->getId() : '0');
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0',
                $params['repository'] . '.specialite = ' . $id_specialite
            );

            $can_add = (! is_null($membre->getSpecialite()) ? true : false);
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'patient_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('patient/index.html.twig', array(
            'patients' => $result['paginator'],
            'can_add' => $can_add,
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des patients'
            )
        ));
    }

    /**
     * Fiche d'un patient
     *
     * @Route("/patient/see/{id}/{page}", name="patient_see")
     * @Route("/patient/ajax/see/{id}/{page}", name="patient_ajax_see")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Patient $patient
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Patient $patient, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('patient/ajax_see.html.twig', array(
                'patient' => $patient
            ));
        }

        return $this->render('patient/see.html.twig', array(
            'page' => $page,
            'patient' => $patient,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('patient_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de patients'
                ),
                'active' => 'Fiche d\'un patient'
            )
        ));
    }

    /**
     * Bloc patient d'une spécialité
     *
     * @Route("/patient/ajax/listing/{id}/{page}", name="patient_ajax_see_liste")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Specialite $specialite, int $page = 1)
    {
        $params = array(
            'field' => 'nom ASC, Patient.prenom',
            'order' => 'ASC',
            'page' => $page,
            'repositoryClass' => Patient::class,
            'repository' => 'Patient',
            'repositoryMethode' => 'findAllPatients'
        );

        $params['condition'] = array(
            $params['repository'] . '.specialite  = ' . $specialite->getId()
        );

        if (! $this->isAdmin()) {
            $params['condition'][] = $params['repository'] . '.disabled = 0';
        }

        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'patient_ajax_see_liste',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#ajax_specialite_patient_see',
            'route_params' => array(
                'id' => $specialite->getId()
            )
        );

        return $this->render('patient/ajax_see_liste.html.twig', array(
            'specialite' => $specialite,
            'patients' => $result['paginator'],
            'pagination' => $pagination,
            'type' => 'specialite'
        ));
    }

    /**
     * Ajout d'un nouveau patient
     *
     * @Route("/patient/add/{page}/{membre_id}", name="patient_add")
     * @ParamConverter("membre", options={"mapping": {"membre_id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page, Membre $membre)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $patient = new Patient();

        // requête pour le champ spécialité
        $specialite = $membre->getSpecialite();
        $sr = $this->getDoctrine()->getRepository(Specialite::class);

        $query = $sr->createQueryBuilder('specialite')->innerJoin('specialite.etablissement', 'etablissement');

        $etablissements = $this->getDoctrine()
            ->getRepository(Etablissement::class)
            ->findEtablissementAvecSpecialite();

        $disabled = false;
        if (! $this->isAdmin() && ! is_null($specialite)) {
            $patient->setSpecialite($membre->getSpecialite());
            $query->andWhere("specialite.id = " . $specialite->getId());
            $disabled = true;
        }

        $query->andWhere('specialite.disabled = 0');
        $query->orderBy('etablissement.nom', 'ASC')->addOrderBy('specialite.service', 'ASC');

        $form = $this->createForm(PatientType::class, $patient, array(
            'disabled_specialite' => $disabled,
            'query_specialite' => $query
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach ($patient->getFamilles() as $famille) {
                // vérification pour éviter les doublons dans les adresses des familles
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

                // Si l'adresse existe on la set sinon on la crée directement pour ne pas créer des doublons
                if (! is_null($adresse)) {
                    $famille->setFamilleAdresse($adresse);
                } else {
                    $famille_adresse = $famille->getFamilleAdresse();
                    $famille_adresse->setDisabled(0);
                    $em->persist($famille_adresse);
                    $em->flush();
                    $famille->setFamilleAdresse($famille_adresse);
                }

                $famille->setPatient($patient);
                $famille->setDisabled(0);
                $patient->addFamille($famille);
            }

            $patient->setDisabled(0);

            $em->persist($patient);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_see', array(
                'id' => $patient->getId()
            )));
        }

        return $this->render('patient/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'etablissements' => $etablissements,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('patient_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de patients'
                ),
                'active' => "Ajout d'un patient"
            )
        ));
    }

    /**
     * Edition d'un patient
     *
     * @Route("/patient/edit/{id}/{page}", name="patient_edit")
     * @Route("/patient/ajax/edit/{id}/{type}/{page}", name="patient_ajax_edit")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Patient $patient
     * @param int $page
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Patient $patient, int $page = 1, string $type = 'patient')
    {
        $arrayFilters = $this->getDatasFilter($session);

        $specialite = $patient->getSpecialite();

        if ($this->isAdmin()) {
            $repositoryE = $this->getDoctrine()->getRepository(Etablissement::class);
            $etablissements = $repositoryE->findEtablissementAvecSpecialite();

            $form = $this->createForm(PatientType::class, $patient, array(
                'add' => false,
                'disabled_specialite' => ($type == 'specialite' ? true : false)
            ));
        } else {
            $etablissements = array();
            $form = $this->createForm(PatientType::class, $patient, array(
                'add' => false,
                'disabled_specialite' => true
            ));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            if (is_null($patient->getSpecialite())) {
                $patient->setSpecialite($specialite);
            }
            $em->persist($patient);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true,
                    'page' => $page
                ));
            }

            return $this->redirect($this->generateUrl('patient_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('patient/ajax_edit.html.twig', [
                'patient' => $patient,
                'form' => $form->createView(),
                'etablissements' => $etablissements,
                'type' => $type,
                'page' => $page
            ]);
        }

        return $this->render('patient/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'patient' => $patient,
            'etablissements' => $etablissements,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('patient_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de patients'
                ),
                'active' => 'Edition de #' . $patient->getId() . ' - ' . $patient->getPrenom() . ' ' . $patient->getNom()
            )
        ));
    }

    /**
     * Désactivation d'un patient
     *
     * @Route("/patient/delete/{id}/{page}", name="patient_delete")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Patient $patient
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Patient $patient, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($patient->getDisabled() == 1) {
            $patient->setDisabled(0);
        } else {
            $patient->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($patient);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('patient_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Mise à jour du dropdown Patient lorsque la spécialité change dans le formulaire d'ajout d'un historique
     *
     * @Route("/patient/ajax/add/dropdown/{id}", name="patient_ajax_add_dropdown", defaults={"id" = 0})
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param Specialite $specialite
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxDropdownAction(Request $request, Specialite $specialite = null)
    {
        if (is_null($specialite)) {
            $patients = array();
        } else {
            $params = array(
                'field' => 'nom ASC, Patient.prenom',
                'order' => 'ASC',
                'page' => 1,
                'repositoryClass' => Patient::class,
                'repository' => 'Patient',
                'condition' => array(
                    'Patient.specialite = ' . $specialite->getId(),
                    'Patient.disabled = 0'
                )
            );

            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $result = $repository->findAllPatients(1, 10, $params);
            $patients = $result['paginator'];
        }

        return $this->render('patient/ajax_dropdown.html.twig', array(
            'patients' => $patients
        ));
    }
}
