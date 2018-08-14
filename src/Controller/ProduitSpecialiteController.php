<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ProduitSpecialite;
use App\Form\ProduitSpecialiteType;
use App\Entity\Specialite;

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
     * @Route("/produit_specialite/ajax/see/{id}/{type}", name="produit_specialite_ajax_see")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction (int $id, string $type)
    {
        switch($type){
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                break;
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        return $this->render('produit_specialite/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Ajout d'un lien produit - spécialité
     *
     * @Route("/produit_specialite/ajax/add/{id}/{type}", name="produit_specialite_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, int $id, string $type)
    {
        $produitSpecialite = new ProduitSpecialite();
        
        switch($type){
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                $methode = 'setProduit';
                $disabled_produit = true;
                $disabled_specialite = false;
                break;
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $methode = 'setSpecialite';
                $disabled_produit = false;
                $disabled_specialite = true;
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        $produitSpecialite->{$methode}($objet);

        $form = $this->createForm(ProduitSpecialiteType::class, $produitSpecialite, array(
            'label_submit' => 'Ajouter',
            'disabled_produit' => $disabled_produit,
            'disabled_specialite' => $disabled_specialite
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
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Edition d'un lien produit - spécialité
     *
     * @Route("/produit_specialite/ajax/edit/{id}/{type}", name="produit_specialite_ajax_edit")
     * @ParamConverter("produitSpecialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param ProduitSpecialite $produitSpecialite
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ProduitSpecialite $produitSpecialite, string $type)
    {
        $form = $this->createForm(ProduitSpecialiteType::class, $produitSpecialite, array(
            'label_submit' => 'Modifier',
            'date_valeur' => $produitSpecialite->getDate(),
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
            'produitSpecialite' => $produitSpecialite,
            'type' => $type
        ));
    }
}
