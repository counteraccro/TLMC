<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\EvenementType;

class EvenementController extends AppController
{
    /**
     * Tableau de types
     *
     * @var array
     */
    const TYPE = array(
        1 => 'Repas',
        2 => 'Sortie',
        3 => 'Spectacle',
        4 => 'Fête',
    );
    
    /**
     * Tableau de statuts
     *
     * @var array
     */
    const STATUT = array(
        1 => 'Ouvert',
        2 => 'Inscription',
        3 => 'Annulé',
        4 => 'Fermé'
    );
    
    /**
     * Listing des événements
     *
     * @Route("/evenement/listing/{page}/{field}/{order}", name="evenement_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Evenement::class,
            'repository' => 'Evenement',
            'repositoryMethode' => 'findAllEvenements'
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
            'route' => 'evenement_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('evenement/index.html.twig', array(
            'evenements' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des événements'
            )
        ));
    }
    
    /**
     * Fiche d'un événement
     *
     * @Route("/evenement/see/{id}/{page}", name="evenement_see")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE')")
     *
     * @param SessionInterface $session
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Evenement $evenement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        return $this->render('evenement/see.html.twig', array(
            'page' => $page,
            'evenement' => $evenement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'événements"
                ),
                'active' => 'Fiche d\'un événement'
            )
        ));
    }
    
    /**
     * Ajout d'un nouvel événement
     *
     * @Route("/evenement/add/{page}", name="evenement_add")
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
        
        $evenement = new Evenement();
        
        $form = $this->createForm(EvenementType::class, $evenement);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $evenement->setImage('https://images-eu.ssl-images-amazon.com/images/I/81xfN64QjVL.png');
            $evenement->setDisabled(0);
            
            $em->persist($evenement);
            $em->flush();
            
            return $this->redirect($this->generateUrl('evenement_listing'));
        }
        
        return $this->render('evenement/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des événements'
                ),
                'active' => "Ajout d'un événement"
            )
        ));
    }
    
    /**
     * Edition d'un événement
     *
     * @Route("/evenement/edit/{id}/{page}", name="evenement_edit")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Evenement $evenement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(EvenementType::class, $evenement);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($evenement);
            $em->flush();
            
            return $this->redirect($this->generateUrl('evenement_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('evenement/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'evenement' => $evenement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de événements'
                ),
                'active' => 'Edition de #' . $evenement->getId() . ' - ' . $evenement->getNom()
            )
        ));
    }
    
    /**
     * Désactivation d'un événement
     *
     * @Route("/evenement/delete/{id}/{page}", name="evenement_delete")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Evenement $evenement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($evenement->getDisabled() == 1) {
            $evenement->setDisabled(0);
        } else {
            $evenement->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($evenement);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('evenement_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
