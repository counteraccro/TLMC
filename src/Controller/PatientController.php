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

class PatientController extends AppController
{

    /**
     * Listing des patients
     *
     * @Route("/patient/listing/{page}/{field}/{order}", name="patient_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
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
            'repositoryClass' => Patient::class,
            'repository' => 'Patient',
            'repositoryMethode' => 'findAllPatients',
            'sans_inactif' => true
        );

        foreach ($this->getUser()->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                $params['sans_inactif'] = false;
                break;
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

        return $this->render('patient/index.html.twig', [
            'controller_name' => 'PatientController',
            'patients' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des patients'
            )
        ]);
    }

    
    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'ajout d'un patient
     * 
     * @Route("/patient/ajax/add/specialite/{id}", name="patient_ajax_add_specialite", defaults={"id" = 0})
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function addAjaxSpecialiteAction(Request $request, Etablissement $etablissement)
    {
        $specialites = $etablissement->getSpecialites();
        
        return $this->render('patient/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => 0
        ));
    }
    
    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'édition d'un patient
     * 
     * @Route("/patient/ajax/edit/specialite/{patient_id}/{etablissement_id}", name="patient_ajax_edit_specialite", defaults={"etablissement_id"=0})
     * @ParamConverter("patient", options={"mapping": {"patient_id": "id"}})
     * @ParamConverter("etablissement", options={"mapping": {"etablissement_id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function editAjaxSpecialiteAction(Request $request, Patient $patient, int $etablissement = null)
    {
        $select_specialite = $patient->getSpecialite()->getId();
        
        if (! is_null($etablissement)) {
            $specialites = $etablissement->getSpecialites();
        } else {
            $specialites = $patient->getEtablissement()->getSpecialites();
        }
        
        return $this->render('patient/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => $select_specialite
        ));
    }
    
    /**
     * Fiche d'un patient
     *
     * @Route("/patient/see/{id}/{page}", name="patient_see")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function seeAction(SessionInterface $session, Patient $patient, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('patient/see.html.twig', [
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
        ]);
    }

    /**
     * Bloc famille d'un patient
     *
     * @Route("/patient/ajax/see/{id}", name="patient_famille_ajax_see")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxSeeAction(Patient $patient)
    {
        $familles = $this->getElementsLiesActifs($patient, 'getFamilles');

        return $this->render('patient/ajax_see_famille.html.twig', [
            'patient' => $patient,
            'familles' => $familles
        ]);
    }

    /**
     * Ajout d'un nouveau patient
     *
     * @Route("/patient/add/{page}", name="patient_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $patient = new Patient();
        
        $form = $this->createForm(PatientType::class, $patient);

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
        
        $repository = $this->getDoctrine()->getRepository(Etablissement::class);
        $etablissements = $repository->findAll();
        
        return $this->render('patient/add.html.twig', [
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
        ]);
    }

    /**
     * Edition d'un patient
     *
     * @Route("/patient/edit/{id}/{page}", name="patient_edit")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function editAction(SessionInterface $session, Request $request, Patient $patient, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(PatientType::class, $patient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($patient);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        return $this->render('patient/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
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
                'active' => 'Edition de #' . $patient->getId() . ' - ' . $patient->getPrenom() . ' ' . $patient->getNom()
            )
        ]);
    }

    /**
     * desactivation d'un patient
     *
     * @Route("/patient/delete/{id}/{page}", name="patient_delete")
     * @ParamConverter("patient", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(SessionInterface $session, Patient $patient, $page)
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
