<?php
namespace App\Controller;

use App\Entity\Temoignage;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TemoignageType;
use App\Entity\Membre;
use App\Entity\Evenement;
use App\Entity\Produit;

class TemoignageController extends AppController
{

    /**
     * Listing des témoiganges
     *
     * @Route("/temoignage/listing/{page}/{field}/{order}", name="temoignage_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Temoignage::class,
            'repository' => 'Temoignage',
            'repositoryMethode' => 'findAllTemoignages'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();
            $params['condition'] = array(
                array(
                    'key' => 'membre',
                    'value' => $membre->getId()
                ),
                array(
                    'key' => 'disabled',
                    'value' => 0
                )
            );
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'temoignage_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('temoignage/index.html.twig', array(
            'controller_name' => 'TemoignageController',
            'temoignages' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des témoignages'
            )
        ));
    }

    /**
     * Fiche d'un témoignage
     *
     * @Route("/temoignage/see/{id}/{page}", name="temoignage_see")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Temoignage $temoignage
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Temoignage $temoignage, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('temoignage/see.html.twig', array(
            'page' => $page,
            'temoignage' => $temoignage,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de témoignages"
                ),
                'active' => 'Fiche d\'un témoignage'
            )
        ));
    }
    
    /**
     * Bloc témoignage d'un membre / d'un événement / d'un produit
     *
     * @Route("/membre/ajax/see/{id}/{type}", name="temoignage_ajax_see")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE')")
     *
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type)
    {
        
        switch($type){
            case 'membre':
                $repository = $this->getDoctrine()->getRepository(Membre::class);
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                break;
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        $temoignages = $this->getElementsLiesActifs($objet, 'getTemoignages');
        
        return $this->render('temoignage/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type,
            'temoignages' => $temoignages
        ));
    }

    /**
     * Ajout d'une nouveau témoignage
     *
     * @Route("/temoignage/add/{page}", name="temoignage_add")
     * @Route("/temoignage/ajax/add/{id}/{type}", name="temoignage_ajax_add")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = 0, string $type)
    {
        $arrayFilters = $this->getDatasFilter($session);
        $opt_form = array('label_submit' => 'Ajouter');
        
        $temoignage = new Temoignage();
        
        if ($request->isXmlHttpRequest()) {
            switch($type){
                case 'evenement':
                    $repository = $this->getDoctrine()->getRepository(Evenement::class);
                    $objets = $repository->findById($id);
                    $objet = $objets[0];
                    
                    $opt_form['disabled_event'] = true;
                    $opt_form['avec_prod'] = false;
                    
                    $temoignage->setEvenement($objet);
                    break;
                case 'produit':
                    $repository = $this->getDoctrine()->getRepository(Produit::class);
                    $objets = $repository->findById($id);
                    $objet = $objets[0];
                    
                    $opt_form['disabled_event'] = true;
                    $opt_form['avec_prod'] = false;
                    
                    $temoignage->setProduit($objet);
                    break;
            }
            
        }
        
        $form = $this->createForm(TemoignageType::class, $temoignage, $opt_form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $temoignage->setMembre($this->getMembre());
            $temoignage->setDisabled(0);
            $temoignage->setDateCreation(new \DateTime());
            $em->persist($temoignage);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('temoignage_listing'));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('temoignage/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'objet' => $objet,
                'type' => $type
            ));
        }

        return $this->render('temoignage/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des témoignages'
                ),
                'active' => "Ajout d'un témoignage"
            )
        ));
    }

    /**
     * Edition d'un témoignage
     *
     * @Route("/temoignage/edit/{id}/{page}", name="temoignage_edit")
     * @Route("/temoignage/ajax/edit/{id}/{objet_id}/{type}", name="temoignage_ajax_edit")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Temoignage $temoignage
     * @param int $page
     * @param int $objet_id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Temoignage $temoignage, int $page = 1, int $objet_id = 0, string $type)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(TemoignageType::class, $temoignage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($temoignage);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }

            return $this->redirect($this->generateUrl('temoignage_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {
            return $this->render('temoignage/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'temoignage' => $temoignage
            ));
        }

        return $this->render('temoignage/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'temoignage' => $temoignage,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des témoignages'
                ),
                'active' => 'Edition de #' . $temoignage->getId() . ' - ' . $temoignage->getTitre()
            )
        ));
    }

    /**
     * Désactivation d'un témoignage
     *
     * @Route("/temoignage/delete/{id}/{page}", name="temoignage_delete")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Temoignage $temoignage
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Temoignage $temoignage, $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($temoignage->getDisabled() == 1) {
            $temoignage->setDisabled(0);
        } else {
            $temoignage->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($temoignage);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->redirectToRoute('temoignage_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
