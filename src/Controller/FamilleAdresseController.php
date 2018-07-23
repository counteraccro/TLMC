<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FamilleAdresse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\FamilleAdresseType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FamilleAdresseController extends AppController
{
    /**
     * Listing des adresses des familles
     * @Route("/famille_adresse/listing/{page}/{field}/{order}", name="famille_adresse", defaults={"page" = 1, "field"= null, "order"= null})
     */
    public function index(Request $request, SessionInterface $session, int $page = 1, $field = null, $order = null)
    {
        if (is_null($field)) {
            $field = 'id';
        }
        
        if (is_null($order)) {
            $order = 'DESC';
        }
        
        //$session->set(self::CURRENT_SEARCH, array());
        
        $params = array(
            'field' => $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => FamilleAdresse::class,
            'repository' => 'FamilleAdresse',
            'repositoryMethode' => 'findAllFamilleAdresses'
        );
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'famille_adresse',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('famille_adresse/index.html.twig', [
            'controller_name' => 'FamilleAdresseController',
            'adresses' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste d'adresses de famille"
            )
        ]);
    }
    
    /**
     * Ajout d'une adresse
     * 
     * @Route("/famille_adresse/add/{page}", name="famille_adresse_add")
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $adresse = new FamilleAdresse();
        
        $form = $this->createForm(FamilleAdresseType::class, $adresse);
        $form->add('save', SubmitType::class, array(
            'label' => 'Ajouter'
        ));
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($adresse);
            $em->flush();
            
            return $this->redirect($this->generateUrl('famille_adresse'));
        }
        
        return $this->render('famille_adresse/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_adresse', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'adresses"
                ),
                'active' => "Ajout d'une adresse"
            )
        ]);
    }
}
