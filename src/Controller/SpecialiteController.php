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
     * @Route("/specialite/ajax/see/{id}", name="specialite_ajax_see")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     * 
     * @param Request $request
     * @param SessionInterface $session
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Specialite $specialite, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($request->isXmlHttpRequest()) {
            
            return $this->render('specialite/ajax_see.html.twig', array(
                'specialite' => $specialite
            ));
        }
        
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
            $etablissement = $repository->findOneBy(array('id' => $id));
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
     * @Route("/specialite/ajax/edit/{id}/{type}", name="specialite_ajax_edit")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Specialite $specialite
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Specialite $specialite, int $page = 1, string $type = null)
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
                'specialite' => $specialite,
                'type' => $type
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
     * Bloc spécialité dans la vue d'un établissement
     *
     * @Route("/specialite/etablissement/ajax/see/{id}/{page}", name="specialite_etablissement_ajax_see")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Etablissement $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Etablissement $etablissement, int $page = 1)
    {
        $params = array(
            'field' => 'service',
            'order' => 'ASC',
            'page' => $page,
            'repositoryClass' => Specialite::class,
            'repository' => 'Specialite',
            'repositoryMethode' => 'findAllSpecialites',
        );
        
        $params['condition'] = array(
            $params['repository'] . '.etablissement = ' . $etablissement->getId()
        );
        
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'specialite_etablissement_ajax_see',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#ajax_etablissement_specialite_see',
            'route_params' => array(
                'id' => $etablissement->getId()
            )
        );
        
        return $this->render('specialite/ajax_see_liste.html.twig', array(
            'etablissement' => $etablissement,
            'specialites' => $result['paginator'],
            'pagination' => $pagination
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'ajout d'un membre ou d'un patient
     *
     * @Route("/specialite/ajax/add/dropdown/{type}/{id}", name="specialite_ajax_add_dropdown", defaults={"id" = 0})
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param Etablissement $etablissement
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxDropdownAction(Request $request, Etablissement $etablissement, string $type)
    {
        $specialites = $this->getDoctrine()->getRepository(Specialite::class)->getSpecialitesByEtablissement($etablissement);

        return $this->render('specialite/ajax_dropdown.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => 0,
            'type' => $type
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'édition d'un membre
     *
     * @Route("/specialite/ajax/edit_dropdown/{id}/{type}/{etablissement_id}", name="specialite_ajax_edit_dropdown",)
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @param int $etablissement_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxDropdownAction(Request $request, int $id, string $type, int $etablissement_id = 0)
    {
        // récupération de l'objet
        switch ($type) {
            case 'membre':
                $repo_membre = $this->getDoctrine()->getRepository(Membre::class);
                $objet = $repo_membre->findOneBy(array('id' => $id));

                $etablissement = $objet->getEtablissement();
                break;
        }

        $select_specialite = (! is_null($objet->getSpecialite()) ? $objet->getSpecialite()->getId() : 0);

        if ($etablissement_id) {
            $etablissement = $this->getDoctrine()->getRepository(Etablissement::class)->findOneBy(array('id' => $etablissement_id));
        }
        
        $repository = $this->getDoctrine()->getRepository(Specialite::class);
        $specialites = $repository->getSpecialitesByEtablissement($etablissement);

        return $this->render('specialite/ajax_dropdown.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => $select_specialite,
            'type' => $type
        ));
    }
}
