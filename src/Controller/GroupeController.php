<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Groupe;

class GroupeController extends AppController
{
    /**
     * @Route("/groupe", name="groupe")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Groupe::class);
        
        $groupes = $repository->findAll();
        
        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
            'groupes' => $groupes
        ]);
    }
    
     /**
     * Récupère la liste des groupes de l'utilisateur courant
     *
     * @Route("/groupe/ajax/listegroupes", name="groupe_ajax_listegroupes")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxListeGroupesAction()
    {
        $repository = $this->getDoctrine()->getRepository(Groupe::class);
        $listeGroupes = $repository->findByUser($this->getUser()->getId());
        
        return $this->json(array(
            'listeGroupes' => $listeGroupes
        ));
    }
}
   