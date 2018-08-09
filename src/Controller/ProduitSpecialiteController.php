<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ProduitSpecialite;
use App\Form\ProduitSpecialiteType;

class ProduitSpecialiteController extends AppController
{

    /**
     *
     * @Route("/produit/specialite", name="produit_specialite")
     */
    public function index()
    {
        return $this->render('produit_specialite/index.html.twig', [
            'controller_name' => 'ProduitSpecialiteController'
        ]);
    }

    /**
     * Bloc produit - spécialité dans la vue d'un produit
     *
     * @Route("/produit_specialite/ajax/see/{id}", name="produit_specialite_ajax_see")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Produit $produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Produit $produit)
    {
        return $this->render('produit_specialite/ajax_see.html.twig', array(
            'produit' => $produit
        ));
    }

    /**
     * Ajout d'un lien produit - spécialité
     *
     * @Route("/produit_specialite/ajax/add/{id}", name="produit_specialite_ajax_add")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Produit $produit)
    {
        $produitSpecialite = new ProduitSpecialite();

        $produitSpecialite->setProduit($produit);

        $form = $this->createForm(ProduitSpecialiteType::class, $produitSpecialite, array(
            'label_submit' => 'Ajouter',
            'disabled_produit' => true
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($produitSpecialite);
            $em->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('produit_specialite/ajax_add.html.twig', array(
            'form' => $form->createView(),
            'produit' => $produit
        ));
    }

    /**
     * Edition d'un lien produit - spécialité
     *
     * @Route("/produit_specialite/ajax/edit/{id}", name="produit_specialite_ajax_edit")
     * @ParamConverter("produitSpecialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param ProduitSpecialite $produitSpecialite
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ProduitSpecialite $produitSpecialite)
    {
        $form = $this->createForm(ProduitSpecialiteType::class, $produitSpecialite, array(
            'label_submit' => 'Modifier',
            'disabled_produit' => true,
            'disabled_specialite' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($produitSpecialite);
            $em->flush();

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('produit_specialite/ajax_edit.html.twig', array(
            'form' => $form->createView(),
            'produitSpecialite' => $produitSpecialite
        ));
    }
}
