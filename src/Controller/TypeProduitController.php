<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\TypeProduit;
use App\Form\TypeProduitType;

class TypeProduitController extends AppController
{
    /**
     * Listing des types de produit
     *
     * @Route("/type_produit/listing/{page}/{field}/{order}", name="type_produit_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => TypeProduit::class,
            'repository' => 'TypeProduit',
            'repositoryMethode' => 'findAllTypeProduits'
        );
        
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'type_produit_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('type_produit/index.html.twig', array(
            'types' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des types de produit'
            )
        ));
    }
    
    /**
     * Ajout d'un type de produit
     *
     * @Route("/type_produit/add/{page}", name="type_produit_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $type_evenement = new TypeProduit();
        
        $form = $this->createForm(TypeProduitType::class, $type_evenement, array('label_submit' => 'Ajouter'));
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $type_evenement->setDisabled(0);
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($type_evenement);
            $em->flush();
            
            return $this->redirect($this->generateUrl('type_produit_listing'));
        }
        
        return $this->render('type_produit/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('type_produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion des types de produit"
                ),
                'active' => "Ajout d'un type de produit"
            )
        ));
    }
    
    /**
     * Edition d'un type de produit
     *
     * @Route("/type_produit/edit/{id}/{page}", name="type_produit_edit")
     * @ParamConverter("type_produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param TypeProduit $type_produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, TypeProduit $type_produit, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(TypeProduitType::class, $type_produit, array(
            'label_submit' => 'Modifier'
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($type_produit);
            $em->flush();
            
            return $this->redirect($this->generateUrl('type_produit_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('type_produit/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'type' => $type_produit,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('type_produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de types de produits'
                ),
                'active' => 'Edition de #' . $type_produit->getId() . ' - ' . $type_produit->getNom()
            )
        ));
    }
    
    /**
     * Désactivation d'un type de produit
     *
     * @Route("/type_produit/delete/{id}/{page}", name="type_produit_delete")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param TypeProduit $type_produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, TypeProduit $type_produit, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($type_produit->getDisabled() == 1) {
            $type_produit->setDisabled(0);
        } else {
            $type_produit->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($type_produit);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('type_produit_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
