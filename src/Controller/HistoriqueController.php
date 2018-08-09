<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Historique;
use App\Entity\Specialite;
use App\Entity\Evenement;
use App\Entity\Patient;
use Symfony\Component\HttpFoundation\Request;
use App\Form\HistoriqueType;

class HistoriqueController extends AppController
{

    /**
     *
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
        switch ($type) {
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];
                break;
            case 'patient':
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];
                break;
            case 'membre':
                $objet = $this->getMembre();
                break;
        }
        
        return $this->render('historique/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Ajout d'une nouveau témoignage
     *
     * @Route("/historique/ajax/add/{id}/{type}", name="historique_ajax_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxAddAction(Request $request, int $id = 0, string $type = null)
    {
        $opt_form = array(
            'label_submit' => 'Ajouter'
        );

        $historique = new Historique();

        switch ($type) {
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];

                $opt_form['disabled_specialite'] = true;

                $historique->setSpecialite($objet);
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];

                $opt_form['disabled_evenement'] = true;

                $historique->setEvenement($objet);
                break;
            case 'patient':
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $objets = $repository->findById($id);
                $objet = $objets[0];
                
                $opt_form['disabled_patient'] = true;
                
                $historique->setPatient($objet);
                break;
            case 'membre':
                $objet = $this->getMembre();
                break;
        }

        $historique->setMembre($this->getMembre());
        
        $form = $this->createForm(HistoriqueType::class, $historique, $opt_form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            $em->persist($historique);
            $em->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('historique/ajax_add.html.twig', array(
            'form' => $form->createView(),
            'objet' => $objet,
            'type' => $type
        ));
    }
    
    /**
     * Edition d'un témoignage
     *
     * @Route("/historique/ajax/edit/{id}/{objet_id}/{type}", name="historique_ajax_edit")
     * @ParamConverter("historique", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param Historique $historique
     * @param int $objet_id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Historique $historique, int $objet_id, string $type = null)
    {
        $opt_form = array('label_submit' => 'Modifier');
        
        switch($type){
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $objets = $repository->findById($objet_id);
                $objet = $objets[0];
                
                $opt_form['disabled_evenement'] = true;
                break;
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $objets = $repository->findById($objet_id);
                $objet = $objets[0];
                
                $opt_form['disabled_specialite'] = true;
                break;
            case 'patient':
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $objets = $repository->findById($objet_id);
                $objet = $objets[0];
                
                $opt_form['disabled_patient'] = true;
                break;
            case 'membre':
                $objet = $this->getMembre();
                break;
        }
        
        $form = $this->createForm(HistoriqueType::class, $historique, $opt_form);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($historique);
            $em->flush();
            
                return $this->json(array(
                    'statut' => true
                ));
           
        }
            return $this->render('historique/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'objet' => $objet,
                'historique' => $historique,
                'type' => $type
            ));
        
    }
}
