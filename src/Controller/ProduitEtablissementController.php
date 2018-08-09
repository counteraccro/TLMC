<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ProduitEtablissement;
use App\Form\ProduitEtablissementType;

class ProduitEtablissementController extends AppController
{
    /**
     * @Route("/produit/etablissement", name="produit_etablissement")
     */
    public function index()
    {
        return $this->render('produit_etablissement/index.html.twig', [
            'controller_name' => 'ProduitEtablissementController',
        ]);
    }
    
    /**
     * Bloc produit - établissement dans la vue d'un produit
     *
     * @Route("/produit_etablissement/ajax/see/{id}", name="produit_etablissement_ajax_see")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Produit $produit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(Produit $produit)
    {
        return $this->render('produit_etablissement/ajax_see.html.twig', array(
            'produit' => $produit        ));
    }
    
    /**
     * Ajout d'un lien produit - établissement
     *
     * @Route("/produit_etablissement/ajax/add/{id}", name="produit_etablissement_ajax_add")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Produit $produit)
    {
        $produitEtablissement = new ProduitEtablissement();
        
        $produitEtablissement->setProduit($produit);
        
        $form = $this->createForm(ProduitEtablissementType::class, $produitEtablissement, array(
            'label_submit' => 'Ajouter',
            'disabled_produit' => true
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($produitEtablissement);
            $em->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->render('produit_etablissement/ajax_add.html.twig', array(
            'form' => $form->createView(),
            'produit' => $produit
        ));
    }
    
    /**
     * Edition d'un lien produit - établissement
     *
     * @Route("/produit_etablissement/ajax/edit/{id}", name="produit_etablissement_ajax_edit")
     * @ParamConverter("produitEtablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param ProduitEtablissement $produitEtablissement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ProduitEtablissement $produitEtablissement)
    {
        $form = $this->createForm(ProduitEtablissementType::class, $produitEtablissement, array(
            'label_submit' => 'Modifier',
            'disabled_produit' => true,
            'disabled_etablissement' => true
        ));
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($produitEtablissement);
            $em->flush();
            
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->render('produit_etablissement/ajax_edit.html.twig', array(
            'form' => $form->createView(),
            'produitEtablissement' => $produitEtablissement
        ));
    }
}
