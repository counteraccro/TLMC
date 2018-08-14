<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Evenement;
use App\Entity\SpecialiteEvenement;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SpecialiteEvenementType;
use App\Entity\Specialite;

class SpecialiteEvenementController extends AppController
{

    /**
     * Tableau de statut
     *
     * @var array
     */
    const STATUT = array(
        0 => 'En cours',
        1 => 'Annulé',
        2 => 'Fini'
    );

    /**
     *
     * @Route("/specialite_evenement", name="specialite_evenement")
     */
    public function index()
    {
        return $this->render('specialite_evenement/index.html.twig', array(
            'controller_name' => 'SpecialiteEvenementController'
        ));
    }

    /**
     * Bloc spécialité - événement dans la vue d'un événement
     *
     * @Route("/specialite_evenement/ajax/see/{id}/{type}", name="specialite_evenement_ajax_see")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Evenement $evenement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type)
    {
        switch($type){
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                break;
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        return $this->render('specialite_evenement/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Ajout d'un lien spécialité - événement
     *
     * @Route("/specialite_evenement/ajax/add/{id}/{type}", name="specialite_evenement_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, int $id, string $type)
    {
        $specialiteEvenement = new SpecialiteEvenement();

        switch($type){
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $methode = 'setEvenement';
                $disabled_evenement = true;
                $disabled_specialite = false;
                break;
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $methode = 'setSpecialite';
                $disabled_evenement = false;
                $disabled_specialite = true;
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        $specialiteEvenement->{$methode}($objet);

        $form = $this->createForm(SpecialiteEvenementType::class, $specialiteEvenement, array(
            'label_submit' => 'Ajouter',
            'disabled_event' => $disabled_evenement,
            'disabled_specialite' => $disabled_specialite
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($specialiteEvenement);
            $em->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('specialite_evenement/ajax_add.html.twig', array(
            'form' => $form->createView(),
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Edition d'un lien spécialité - événement
     *
     * @Route("/specialite_evenement/ajax/edit/{id}/{type}", name="specialite_evenement_ajax_edit")
     * @ParamConverter("specialiteEvenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SpecialiteEvenement $specialiteEvenement
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, SpecialiteEvenement $specialiteEvenement, string $type)
    {
        $form = $this->createForm(SpecialiteEvenementType::class, $specialiteEvenement, array(
            'label_submit' => 'Modifier',
            'date_valeur' => $specialiteEvenement->getDate(),
            'disabled_event' => true,
            'disabled_specialite' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($specialiteEvenement);
            $em->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('specialite_evenement/ajax_edit.html.twig', array(
            'form' => $form->createView(),
            'specialiteEvenement' => $specialiteEvenement,
            'type' => $type
        ));
    }
}
