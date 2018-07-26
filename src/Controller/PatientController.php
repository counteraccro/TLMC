<?php
namespace App\Controller;

use App\Entity\Patient;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PatientType;
use App\Entity\Specialite;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
            'repositoryMethode' => 'findAllPatients'
        );
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
        return $this->render('patient/ajax_see_famille.html.twig', [
            'patient' => $patient
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

            // TODO A changer
            $repository = $this->getDoctrine()->getRepository(Specialite::class);
            $result = $repository->findById(self::ID_SPECIALITE);
            $patient->setSpecialite($result[0]);
            
            foreach ($patient->getFamilles() as $famille) {
                $famille->setPatient($patient);
                $famille->getFamilleAdresse()->setDisabled(0);
                $famille->setDisabled(0);
                $patient->addFamille($famille) ;
            }
            
            $patient->setDisabled(0);

            $em->persist($patient);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_listing'));
        }

        return $this->render('patient/add.html.twig', [
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
