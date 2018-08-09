<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Historique;
use App\Entity\Specialite;
use App\Entity\Evenement;
use App\Entity\Patient;
use App\Entity\Membre;

class HistoriqueController extends AppController
{
    /**
     * @Route("/historique", name="historique")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Historique::class);
        
        $historiques = $repository->findAll();
        
        return $this->render('historique/index.html.twig', [
            'controller_name' => 'HistoriqueController',
            'historiques' => $historiques
        ]);
    }
    
    /**
     * Bloc historique 
     *
     * @Route("/historique/ajax/see/{id}/{type}", name="historique_ajax_see")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param int $id
     * @param string type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type)
    {
        switch($type){
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                break;
            case 'patient':
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                break;
            case 'membre':
                $repository = $this->getDoctrine()->getRepository(Membre::class);
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        return $this->render('historique/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type
        ));
    }
}
