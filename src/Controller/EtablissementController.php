<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Etablissement;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EtablissementType;

class EtablissementController extends AppController
{
    /**
     * Listing des établissements
     * 
     * @Route("/etablissement/listing/{page}/{field}/{order}", name="etablissement_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN')")
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
            'repositoryClass' => Etablissement::class,
            'repository' => 'Etablissement',
            'repositoryMethode' => 'findAllEtablissements',
            'sans_inactif' => false
        );
        
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'etablissement_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('etablissement/index.html.twig', [
            'controller_name' => 'EtablissementController',
            'etablissements' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des établissements'
            )
        ]);
    }
    
    /**
     * Fiche d'un établissement
     *
     * @Route("/etablissement/see/{id}/{page}", name="etablissement_see")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function seeAction(SessionInterface $session, Etablissement $etablissement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        return $this->render('etablissement/see.html.twig', [
            'page' => $page,
            'etablissement' => $etablissement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'établissements"
                ),
                'active' => 'Fiche d\'un établissement'
            )
        ]);
    }
    
    /**
     * Bloc spécialité d'un établissement
     *
     * @Route("/etablissement/ajax/see/{id}", name="etablissement_specialite_ajax_see")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function ajaxSeeAction(Etablissement $etablissement)
    {
        $specialites = $this->getElementsLiesActifs($etablissement, 'getSpecialites');
        
        return $this->render('etablissement/ajax_see_specialite.html.twig', [
            'etablissement' => $etablissement,
            'specialites' => $specialites
        ]);
    }
    
    
    /**
     * Ajout d'un nouveau établissement
     *
     * @Route("/etablissement/add/{page}", name="etablissement_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $etablissement = new Etablissement();
        
        $form = $this->createForm(EtablissementType::class, $etablissement);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            // TODO A changer
            $etablissement->setDisabled(0);
            
            $em->persist($etablissement);
            $em->flush();
            
            return $this->redirect($this->generateUrl('etablissement_listing'));
        }
        
        return $this->render('etablissement/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des établissements'
                ),
                'active' => "Ajout d'un établissement"
            )
        ]);
    }
    
    /**
     * Edition d'un établissement
     *
     * @Route("/etablissement/edit/{id}/{page}", name="etablissement_edit")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(SessionInterface $session, Request $request, Etablissement $etablissement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(EtablissementType::class, $etablissement);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($etablissement);
            $em->flush();
            
            return $this->redirect($this->generateUrl('etablissement_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('etablissement/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'etablissement' => $etablissement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de établissements'
                ),
                'active' => 'Edition de #' . $etablissement->getId() . ' - ' . $etablissement->getNom()
            )
        ]);
    }
    
    /**
     * désactivation d'un établissement
     *
     * @Route("/etablissement/delete/{id}/{page}", name="etablissement_delete")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(SessionInterface $session, Etablissement $etablissement, $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($etablissement->getDisabled() == 1) {
            $etablissement->setDisabled(0);
        } else {
            $etablissement->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($etablissement);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('etablissement_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
