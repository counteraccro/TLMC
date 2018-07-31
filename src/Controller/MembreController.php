<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\MembreType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MembreController extends AppController
{
    /**
     * Listing des membres
     * 
     * @Route("/membre/listing/{page}/{field}/{order}", name="membre_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Membre::class,
            'repository' => 'Membre',
            'repositoryMethode' => 'findAllMembres',
            'sans_inactif' => false
        );
        
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'membre_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('membre/index.html.twig', [
            'controller_name' => 'MembreController',
            'membres' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des membres'
            )
        ]);
    }
    
    /**
     * Fiche d'un membre
     *
     * @Route("/membre/see/{id}/{page}", name="membre_see")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function seeAction(SessionInterface $session, Membre $membre, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        return $this->render('membre/see.html.twig', [
            'page' => $page,
            'membre' => $membre,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de membres'
                ),
                'active' => 'Fiche d\'un membre'
            )
        ]);
    }
    
    /**
     * Ajout d'un nouveau membre
     *
     * @Route("/membre/add/{page}", name="membre_add")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(UserPasswordEncoderInterface $encoder, SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $membre = new Membre();
        
        $form = $this->createForm(MembreType::class, $membre);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            // TODO A changer
            $membre->setDisabled(0);
            $membre->setSalt('41df4dgv54gfd5g');
            $membre->setRoles(array("ROLE_BENEFICIAIRE"));
            
            $encodePassword = $encoder->encodePassword($membre, $membre->getPassword());
            $membre->setPassword($encodePassword);
            $em->persist($membre);
            $em->flush();
            
            return $this->redirect($this->generateUrl('membre_listing'));
        }
        
        return $this->render('membre/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des membres'
                ),
                'active' => "Ajout d'un membre"
            )
        ]);
    }
    
    /**
     * Edition d'un membre
     *
     * @Route("/membre/edit/{id}/{page}", name="membre_edit")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(SessionInterface $session, Request $request, Membre $membre, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(MembreType::class, $membre);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($membre);
            $em->flush();
            
            return $this->redirect($this->generateUrl('membre_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('membre/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'membre' => $membre,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de membres'
                ),
                'active' => 'Edition de #' . $membre->getId() . ' - ' . $membre->getPrenom() . ' ' . $membre->getNom()
            )
        ]);
    }
    
    /**
     * desactivation d'un membre
     *
     * @Route("/patient/delete/{id}/{page}", name="membre_delete")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(SessionInterface $session, Membre $membre, $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($membre->getDisabled() == 1) {
            $membre->setDisabled(0);
        } else {
            $membre->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($membre);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('membre_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
