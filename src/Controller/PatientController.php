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

class PatientController extends AppController
{

    /**
     * Listing des patients
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
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0'
            );

            if (! is_null($membre->getSpecialite())) {
                $params['condition'][] = $params['repository'] . '.specialite = ' . $membre->getSpecialite()->getId();
            }
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
     * @Route("/patient/ajax/see/{id}", name="patient_ajax_see")
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
        $sr = $this->getDoctrine()->getRepository(Specialite::class);
        $query = $sr->createQueryBuilder('specialite')->innerJoin('specialite.etablissement', 'etablissement');
        if ($membre->getSpecialite()) {
            $query->andWhere("etablissement.id = " . $membre->getEtablissement()
                ->getId());
        }
        $query->orderBy('etablissement.nom, specialite.service', 'ASC');

        if (! $this->isAdmin()) {
            if (! is_null($membre->getSpecialite())) {
                $patient->setSpecialite($membre->getSpecialite());

                $form = $this->createForm(PatientType::class, $patient, array(
                    'disabled_specialite' => true,
                    'query_specialite' => $query
                ));
            } else {

                $form = $this->createForm(PatientType::class, $patient, array(
                    'query_specialite' => $query
                ));
            }
        } else {
            $form = $this->createForm(PatientType::class, $patient);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach ($patient->getFamilles() as $famille) {
                $famille->setPatient($patient);
                $famille->getFamilleAdresse()->setDisabled(0);
                $famille->setDisabled(0);
                $patient->addFamille($famille);
            }

            $patient->setDisabled(0);

            $em->persist($patient);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_listing'));
        }

        return $this->render('patient/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
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
     * @Route("/patient/ajax/edit/{id}", name="patient_ajax_edit")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Patient $patient
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Patient $patient, int $page = 0)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($this->isAdmin()) {
            $repositoryE = $this->getDoctrine()->getRepository(Etablissement::class);
            $etablissements = $repositoryE->findHopital();
            $form = $this->createForm(PatientType::class, $patient, array(
                'add' => false
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

            $em->persist($patient);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
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
                'etablissements' => $etablissements
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
     * @param SessionInterface $session
     * @param Patient $patient
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Patient $patient, int $page)
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

        return $this->redirectToRoute('patient_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
