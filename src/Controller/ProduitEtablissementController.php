<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use App\Entity\Etablissement;
use App\Entity\ProduitEtablissement;
use App\Form\ProduitEtablissementType;
use Symfony\Component\HttpFoundation\Request;

class ProduitEtablissementController extends AppController
{

    /**
     *
     * @Route("/produit/etablissement", name="produit_etablissement")
     */
    public function index()
    {
        return $this->render('produit_etablissement/index.html.twig', [
            'controller_name' => 'ProduitEtablissementController'
        ]);
    }

    /**
     * Bloc produit - établissement dans la vue d'un produit
     *
     * @Route("/produit_etablissement/ajax/see/{id}/{type}/{page}", name="produit_etablissement_ajax_see")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param int $id
     * @param string type
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type, int $page = 1)
    {
        switch($type){
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                break;
            case 'etablissement':
                $repository = $this->getDoctrine()->getRepository(Etablissement::class);
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        $params = array(
            'field' => 'id',
            'order' => 'ASC',
            'page' => $page,
            'repositoryClass' => ProduitEtablissement::class,
            'repository' => 'ProduitEtablissement',
            'repositoryMethode' => 'findAllProduitEtablissements',
        );
        
        $params['condition'] = array(
            $params['repository'] . '.' . $type . ' = ' . $id
        );
        
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'produit_etablissement_ajax_see',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#bloc_produit_etablissement',
            'route_params' => array(
                'id' => $id,
                'type' => $type
            )
        );
        
        return $this->render('produit_etablissement/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type,
            'pagination' => $pagination,
            'produitEtablissements' => $result['paginator']
        ));
    }

    /**
     * Ajout d'un lien produit - établissement
     *
     * @Route("/produit_etablissement/ajax/add/{id}/{type}", name="produit_etablissement_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, int $id, string $type)
    {
        $produitEtablissement = new ProduitEtablissement();

        switch($type){
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                $methode = 'setProduit';
                $disabled_produit = true;
                $disabled_etablissement = false;
                break;
            case 'etablissement':
                $repository = $this->getDoctrine()->getRepository(Etablissement::class);
                $methode = 'setEtablissement';
                $disabled_produit = false;
                $disabled_etablissement = true;
                break;
        }
        
        $objets = $repository->findById($id);
        $objet = $objets[0];
        
        $produitEtablissement->{$methode}($objet);

        $form = $this->createForm(ProduitEtablissementType::class, $produitEtablissement, array(
            'label_submit' => 'Ajouter',
            'disabled_produit' => $disabled_produit,
            'disabled_etablissement' => $disabled_etablissement
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
            'objet' => $objet,
            'type' => $type
        ));
    }

    /**
     * Edition d'un lien produit - établissement
     *
     * @Route("/produit_etablissement/ajax/edit/{id}/{type}/{page}", name="produit_etablissement_ajax_edit")
     * @ParamConverter("produitEtablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param ProduitEtablissement $produitEtablissement
     * @param string $type
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ProduitEtablissement $produitEtablissement, string $type, int $page = 1)
    {
        $form = $this->createForm(ProduitEtablissementType::class, $produitEtablissement, array(
            'label_submit' => 'Modifier',
            'date_valeur' => $produitEtablissement->getDate(),
            'disabled_produit' => true,
            'disabled_etablissement' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($produitEtablissement);
            $em->flush();

            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->render('produit_etablissement/ajax_edit.html.twig', array(
            'form' => $form->createView(),
            'produitEtablissement' => $produitEtablissement,
            'type' => $type,
            'page' => $page
        ));
    }
}
