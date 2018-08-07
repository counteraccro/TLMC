<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ProduitType;

class ProduitController extends AppController
{
    /**
     * Tableau de types
     *
     * @var array
     */
    const TYPE = array(
        1 => 'Cadeau',
        2 => 'Matériel',
    );
    
    /**
     * Tableau de genre
     *
     * @var array
     */
    const GENRE = array(
        0 => 'Mixte',
        1 => 'Fille',
        2 => 'Garçon'
    );
    
    /**
     * Listing des produits
     *
     * @Route("/produit/listing/{page}/{field}/{order}", name="produit_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE')")
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
            'repositoryClass' => Produit::class,
            'repository' => 'Produit',
            'repositoryMethode' => 'findAllProduits'
        );

        if (! $this->isAdmin()) {
            $params['condition'] = array(
                array(
                    'key' => 'disabled',
                    'value' => 0
                )
            );
        }
        
        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'produit_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('produit/index.html.twig', array(
            'produits' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des produits'
            )
        ));
    }
    
    /**
     * Fiche d'un produit
     *
     * @Route("/produit/see/{id}/{page}", name="produit_see")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE')")
     *
     * @param SessionInterface $session
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Produit $produit, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        return $this->render('produit/see.html.twig', array(
            'page' => $page,
            'produit' => $produit,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de produits"
                ),
                'active' => 'Fiche d\'un produit'
            )
        ));
    }
    
    /**
     * Ajout d'un nouvel produit
     *
     * @Route("/produit/add/{page}", name="produit_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $produit = new Produit();
        
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            foreach ($produit->getProduitEtablissements() as $etablissement){
                $etablissement->setProduit($produit);
            }
            
            $produit->setDateCreation(new \DateTime());
            $produit->setDisabled(0);
            
            $em->persist($produit);
            $em->flush();
            
            return $this->redirect($this->generateUrl('produit_listing'));
        }
        
        return $this->render('produit/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des produits'
                ),
                'active' => "Ajout d'un produit"
            )
        ));
    }
    
    /**
     * Edition d'un produit
     *
     * @Route("/produit/edit/{id}/{page}", name="produit_edit")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Produit $produit, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(ProduitType::class, $produit);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($produit);
            $em->flush();
            
            return $this->redirect($this->generateUrl('produit_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('produit/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'produit' => $produit,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de produits'
                ),
                'active' => 'Edition de #' . $produit->getId() . ' - ' . $produit->getTitre()
            )
        ));
    }
    
    /**
     * Désactivation d'un produit
     *
     * @Route("/produit/delete/{id}/{page}", name="produit_delete")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Produit $produit, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($produit->getDisabled() == 1) {
            $produit->setDisabled(0);
        } else {
            $produit->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($produit);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('produit_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
