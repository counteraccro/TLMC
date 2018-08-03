<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Specialite;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\SpecialiteType;
use App\Entity\Etablissement;
use App\Entity\Membre;
use App\Entity\Patient;

class SpecialiteController extends AppController
{

    /**
     * Listing des spécialités
     *
     * @Route("/specialite/listing/{page}/{field}/{order}", name="specialite_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN')")
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
            'repositoryClass' => Specialite::class,
            'repository' => 'Specialite',
            'repositoryMethode' => 'findAllSpecialites'
        );

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'specialite_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('specialite/index.html.twig', array(
            'specialites' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des spécialités'
            )
        ));
    }

    /**
     * Fiche d'une spécialité
     *
     * @Route("/specialite/see/{id}/{page}", name="specialite_see")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Specialite $specialite, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('specialite/see.html.twig', array(
            'page' => $page,
            'specialite' => $specialite,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des spécialités'
                ),
                'active' => 'Fiche d\'une spécialité'
            )
        ));
    }

    /**
     * Ajout d'une nouvelle spécialité
     *
     * @Route("/specialite/add/{page}", name="specialite_add")
     * @Route("/specialite/ajax/add/{id}", name="specialite_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $specialite = new Specialite();

        if ($request->isXmlHttpRequest()) {
            $repository = $this->getDoctrine()->getRepository(Etablissement::class);
            $result = $repository->findById($id);
            $etablissement = $result[0];
            $specialite->setEtablissement($etablissement);

            $form = $this->createForm(SpecialiteType::class, $specialite, array(
                'label_submit' => 'Ajouter',
                'disabled_etablissement' => true
            ));
        } else {

            $form = $this->createForm(SpecialiteType::class, $specialite, array(
                'label_submit' => 'Ajouter'
            ));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $specialite->setDisabled(0);

            $em->persist($specialite);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('specialite_listing'));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('specialite/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'etablissement' => $etablissement
            ));
        }

        return $this->render('specialite/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des spécialités'
                ),
                'active' => "Ajout d'une spécialité"
            )
        ));
    }

    /**
     * Edition d'une spécialité
     *
     * @Route("/specialite/edit/{id}/{page}", name="specialite_edit")
     * @Route("/specialite/ajax/edit/{id}", name="specialite_ajax_edit")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Specialite $specialite, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(SpecialiteType::class, $specialite, array(
            'label_submit' => 'Modifier',
            'disabled_etablissement' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($specialite);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }

            return $this->redirect($this->generateUrl('specialite_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('specialite/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'specialite' => $specialite
            ));
        }

        return $this->render('specialite/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'specialite' => $specialite,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de spécialités'
                ),
                'active' => 'Edition de #' . $specialite->getId() . ' - ' . $specialite->getService()
            )
        ));
    }

    /**
     * Désactivation d'une spécialité
     *
     * @Route("/specialite/delete/{id}/{page}", name="specialite_delete")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Specialite $specialite, $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($specialite->getDisabled() == 1) {
            $specialite->setDisabled(0);
        } else {
            $specialite->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($specialite);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->redirectToRoute('specialite_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'ajout d'un membre ou d'un patient
     *
     * @Route("/specialite/ajax/add/dropdown/{objet}/{id}", name="ajax_add_dropdown", defaults={"id" = 0})
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param Etablissement $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxSpecialiteAction(Request $request, Etablissement $etablissement, string $objet)
    {
        $specialites = $this->getElementsLiesActifs($etablissement, 'getSpecialites');

        return $this->render($objet . '/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => 0
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'édition d'un membre
     *
     * @Route("/specialite/ajax/edit_membre/{membre_id}/{etablissement_id}", name="membre_ajax_edit_specialite", defaults={"etablissement_id" = 0})
     * @ParamConverter("membre", options={"mapping": {"membre_id": "id"}})
     * @ParamConverter("etablissement", options={"mapping": {"etablissement_id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Membre $membre
     * @param Etablissement $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxMembreAction(Request $request, Membre $membre, Etablissement $etablissement = null)
    {
        $select_specialite = (! is_null($membre->getSpecialite()) ? $membre->getSpecialite()->getId() : 0);

        if (! is_null($etablissement)) {
            $specialites = $etablissement->getSpecialites();
        } else {
            $specialites = $membre->getEtablissement()->getSpecialites();
        }

        return $this->render('membre/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => $select_specialite
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'édition d'un patient
     *
     * @Route("/specialite/ajax/edit_patient/{patient_id}/{etablissement_id}", name="patient_ajax_edit_specialite", defaults={"etablissement_id"=0})
     * @ParamConverter("patient", options={"mapping": {"patient_id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param Patient $patient
     * @param int $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxPatientAction(Request $request, Patient $patient, int $etablissement_id)
    {
        $select_specialite = $patient->getSpecialite()->getId();

        $repository = $this->getDoctrine()->getRepository(Specialite::class);

        if ($etablissement_id) {
            $specialites = $repository->findByEtablissement($etablissement_id);
        } else {
            $specialites = $repository->findAll();
        }

        return $this->render('patient/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => $select_specialite
        ));
    }
}
