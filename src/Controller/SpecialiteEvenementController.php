<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Evenement;
use App\Entity\SpecialiteEvenement;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SpecialiteEvenementType;

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
     * @Route("/specialite_evenement/ajax/see/{id}", name="specialite_evenement_ajax_see")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Evenement $evenement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Evenement $evenement)
    {
        return $this->render('specialite_evenement/ajax_see.html.twig', array(
            'evenement' => $evenement
        ));
    }

    /**
     * Ajout d'un lien spécialité - événement
     *
     * @Route("/specialite_evenement/ajax/add/{id}", name="specialite_evenement_ajax_add")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Evenement $evenement)
    {
        $specialiteEvenement = new SpecialiteEvenement();

        $specialiteEvenement->setEvenement($evenement);

        $form = $this->createForm(SpecialiteEvenementType::class, $specialiteEvenement, array(
            'label_submit' => 'Ajouter',
            'disabled_event' => true
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
            'evenement' => $evenement
        ));
    }

    /**
     * Edition d'un lien spécialité - événement
     *
     * @Route("/specialite_evenement/ajax/edit/{id}", name="specialite_evenement_ajax_edit")
     * @ParamConverter("specialiteEvenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SpecialiteEvenement $specialiteEvenement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, SpecialiteEvenement $specialiteEvenement)
    {
        $form = $this->createForm(SpecialiteEvenementType::class, $specialiteEvenement, array(
            'label_submit' => 'Modifier',
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
            'specialiteEvenement' => $specialiteEvenement
        ));
    }
}
