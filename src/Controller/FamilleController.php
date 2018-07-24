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
        $form->add('save', SubmitType::class, array(
            'label' => 'Ajouter'
        ));
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $repository1 = $this->getDoctrine()->getRepository(Patient::class);
            // TODO A changer
            $patient = $repository1->findById(44);
            $famille->setPatient($patient[0]);
            
            $repository2 = $this->getDoctrine()->getRepository(FamilleAdresse::class);
            // TODO A changer
            $famille_adresse = $repository2->findById(57);
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
}
