<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Famille;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\FamilleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Patient;
use App\Entity\FamilleAdresse;

class FamilleController extends AppController
{
    /**
     * Listing des familles
     * @Route("/famille/listing/{page}/{field}/{order}", name="famille_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
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
            'repositoryClass' => Famille::class,
            'repository' => 'Famille',
            'repositoryMethode' => 'findAllFamilles'
        );
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'famille_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('famille/index.html.twig', [
            'controller_name' => 'FamilleController',
            'familles' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste des familles"
            )
        ]);
    }
    
    /**
     * Affichage de la fiche d'une famille
     *
     * @Route("/famille/see/{id}/{page}", name="famille_see")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Famille $famille, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        return $this->render('famille/see.html.twig', [
            'page' => $page,
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Fiche d'une famille"
            )
        ]);
    }
    
    /**
     * Ajout d'une famille
     *
     * @Route("/famille/add/{page}", name="famille_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $famille = new Famille();
        
        $form = $this->createForm(FamilleType::class, $famille);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $famille->setDisabled(0);
            
            $em = $this->getDoctrine()->getManager();
            
            $repository1 = $this->getDoctrine()->getRepository(Patient::class);
            // TODO A changer
            $patient = $repository1->findById(self::ID_PATIENT);
            $famille->setPatient($patient[0]);
            
            $repository2 = $this->getDoctrine()->getRepository(FamilleAdresse::class);
            // TODO A changer
            $famille_adresse = $repository2->findById(self::ID_FAMILLE_ADRESSE);
            $famille->setFamilleAdresse($famille_adresse[0]);
            
            $em->persist($famille);
            $em->flush();
            
            return $this->redirect($this->generateUrl('famille_listing'));
        }
        
        return $this->render('famille/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Ajout d'une famille"
            )
        ]);
    }
    
    /**
     * Edition d'une famille
     *
     * @Route("/famille/edit/{id}/{page}", name="famille_edit")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Famille $famille, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        $form = $this->createForm(FamilleType::class, $famille);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($famille);
            $em->flush();
            
            return $this->redirect($this->generateUrl('famille_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }
        
        return $this->render('famille/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de familles'
                ),
                'active' => 'Edition de #' . $famille->getId() . ' - ' . $famille->getPrenom() . ' ' . $famille->getNom()
            )
        ]);
    }
    
    /**
     * Désactivation d'une famille
     *
     * @Route("/famille/delete/{id}/{page}", name="famille_delete")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Famille $famille, $page)
    {
        $arrayFilters = $this->getDatasFilter($session);
        
        if ($famille->getDisabled() == 1) {
            $famille->setDisabled(0);
        } else {
            $famille->setDisabled(1);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($famille);
        
        $entityManager->flush();
        
        return $this->redirectToRoute('famille_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
