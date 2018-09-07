<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ProduitType;
use Symfony\Component\Form\FormError;
use App\Entity\ProduitEtablissement;
use App\Entity\ProduitSpecialite;

class ProduitController extends AppController
{

    /**
     * Tableau de types de produit
     *
     * @var array
     */
    const TYPE = array(
        1 => 'Cadeau',
        2 => 'Matériel'
    );

    /**
     * Tableau de genre du produit
     *
     * @var array
     */
    const GENRE = array(
        0 => 'Mixte',
        1 => 'Fille',
        2 => 'Garçon'
    );

    /**
     * Listing des produits. Pour un membre non administrateur, 
     * seul les produits actifs liés à l'établissement ou à la spécialité du membre sont affichés
     *
     * @Route("/produit/listing/{page}/{field}/{order}", name="produit_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')  or is_granted('ROLE_BENEVOLE')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param int $page
     * @param string $field
     * @param string $order
     * @return \Symfony\Component\HttpFoundation\Response
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
            'repositoryClass' => Produit::class,
            'repository' => 'Produit',
            'repositoryMethode' => 'findAllProduits'
        );

        if (! $this->isAdmin()) {
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0'
            );

            if ($this->getMembre()->getSpecialite()) {
                $params['jointure'] = array(
                    array(
                        'oldrepository' => 'Produit',
                        'newrepository' => 'produitSpecialites'
                    )
                );
                $params['condition'][] = 'produitSpecialites.specialite = ' . $this->getMembre()
                    ->getSpecialite()
                    ->getId();
            } else {
                $params['jointure'] = array(
                    array(
                        'oldrepository' => 'Produit',
                        'newrepository' => 'produitEtablissements'
                    )
                );
                $params['condition'][] = 'produitEtablissements.etablissement = ' . $this->getMembre()
                    ->getEtablissement()
                    ->getId();
            }
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'produit_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('produit/index.html.twig', array(
            'produits' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des produits'
            )
        ));
    }

    /**
     * Fiche d'un produit
     *
     * @Route("/produit/see/{id}/{page}", name="produit_see")
     * @Route("/produit/ajax/see/{id}/{page}", name="produit_ajax_see")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Produit $produit, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            //si le membre n'est pas admin, on affiche le nombre de produit disponible dans sa spécialité
            if ($this->isAdmin()) {
                $admin = true;
            } else {
                $admin = false;
                
                $membre = $this->getMembre();
                $etablissement = $membre->getEtablissement();
                $specialite = $membre->getSpecialite();
                
                if (is_null($specialite)) {
                    $repository = $this->getDoctrine()->getRepository(ProduitEtablissement::class);
                    $lien = $repository->findOneBy(array('produit' => $produit, 'etablissement' => $etablissement));
                } else {
                    $repository = $this->getDoctrine()->getRepository(ProduitSpecialite::class);
                    $lien = $repository->findOneBy(array('produit' => $produit, 'specialite' => $specialite));
                }
            }

            return $this->render('produit/ajax_see.html.twig', array(
                'produit' => $produit,
                'admin' => $admin,
                'quantite' => ($admin ? 0 : $lien->getQuantite())
            ));
        }

        return $this->render('produit/see.html.twig', array(
            'page' => $page,
            'produit' => $produit,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de produits"
                ),
                'active' => 'Fiche d\'un produit'
            )
        ));
    }

    /**
     * Ajout d'un nouvel produit
     *
     * @Route("/produit/add/{page}", name="produit_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $produit = new Produit();
        $produit->setDateEnvoi(new \DateTime());

        $form = $this->createForm(ProduitType::class, $produit, array(
            'add' => true
        ));

        $form->handleRequest($request);

        // vérification que la quantité de produit est supérieur à la somme des quantités de produit dans les différentes spécialités
        if ($form->isSubmitted()) {
            if ($this->totalProduitsEnvoyes($produit) > $produit->getQuantite()) {
                $form->addError(new FormError('La somme des quantités de produits envoyés est supérieure à la quantité de produit'));
            } else {

                //traitement de l'image
                $file = $form['image']->getData();
                if (! is_null($file)) {
                    $fileName = $this->telechargerImage($file, 'produit', $produit->getTitre());
                    if ($fileName) {
                        $produit->setImage($fileName);
                    } else {
                        $form->addError(new FormError("Le fichier n'est pas au format autorisé (jpg, jpeg,png)."));
                    }
                }
            }
            
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                foreach ($produit->getProduitEtablissements() as $produitEtablissement) {
                    $produitEtablissement->setProduit($produit);
                    $produitEtablissement->setDate(new \DateTime());
                }

                foreach ($produit->getProduitSpecialites() as $produitSpecialite) {
                    $produitSpecialite->setProduit($produit);
                    $produitSpecialite->setDate(new \DateTime());
                }
                
                $index = 1;
                foreach ($produit->getExtensionFormulaires() as $extension) {
                    $extension->setOrdre($index);
                    $extension->setDisabled(0);
                    $extension->setProduit($produit);
                    $index++;
                }

                $produit->setDateCreation(new \DateTime());
                $produit->setDisabled(0);

                $em->persist($produit);
                $em->flush();

                return $this->redirect($this->generateUrl('produit_listing'));
            }
        }

        return $this->render('produit/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des produits'
                ),
                'active' => "Ajout d'un produit"
            )
        ));
    }

    /**
     * Edition d'un produit
     *
     * @Route("/produit/edit/{id}/{page}", name="produit_edit")
     * @Route("/produit/ajax/edit/{id}/{page}", name="produit_ajax_edit")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Produit $produit, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $image = $produit->getImage();

        $form = $this->createForm(ProduitType::class, $produit, array(
            'ajax' => ($request->isXmlHttpRequest() ? true : false)
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //vérification du nombre de produits
            if ($this->totalProduitsEnvoyes($produit) > $produit->getQuantite()) {
                $form->addError(new FormError('La somme des quantités de produits envoyés est supérieure à la quantité de produit'));
            } elseif (! $request->isXmlHttpRequest()) {
                //traitement de l'image
                if (is_null($produit->getImage()) && ! is_null($image)) {
                    $produit->setImage($image);
                } elseif(!is_null($produit->getImage())) {
                    $file = $request->files->get('produit')['image'];
                    $fileName = $this->telechargerImage($file, 'produit', $produit->getTitre(), $image);
                    if ($fileName) {
                        $produit->setImage($fileName);
                    } else {
                        $error = new FormError("Le fichier n'est pas au format autorisé (jpg, jpeg,png).");
                        $form->addError($error);
                    }
                }
            }

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $index = 1;
                foreach ($produit->getExtensionFormulaires() as $extension) {
                    if(is_null($extension->getProduit())){
                        $extension->setOrdre($index);
                        $extension->setDisabled(0);
                        $extension->setProduit($produit);
                    }
                    $index++;
                }
                
                $tranche_age = $request->request->get('produit')['tranche_age'];
                asort($tranche_age);
                $produit->setTrancheAge($tranche_age);

                $em->persist($produit);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json(array(
                        'statut' => true
                    ));
                }

                return $this->redirect($this->generateUrl('produit_listing', array(
                    'page' => $page,
                    'field' => $arrayFilters['field'],
                    'order' => $arrayFilters['order']
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('produit/ajax_edit.html.twig', [
                'produit' => $produit,
                'form' => $form->createView()
            ]);
        }

        return $this->render('produit/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'produit' => $produit,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('produit_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de produits'
                ),
                'active' => 'Edition de #' . $produit->getId() . ' - ' . $produit->getTitre()
            )
        ));
    }

    /**
     * Désactivation d'un produit
     *
     * @Route("/produit/delete/{id}/{page}", name="produit_delete")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Produit $produit, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($produit->getDisabled() == 1) {
            $produit->setDisabled(0);
        } else {
            $produit->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($produit);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('produit_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Affichage des établissements et des spécialités dans lesquels sont proposés le produit
     *
     * @Route("/produit/lien/ajax/see/{id}/{page}", name="produit_lien_ajax_see")
     * @ParamConverter("produit", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Produit $produit
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeLienAction(Request $request, Produit $produit, int $page = 1)
    {
        $repository = $this->getDoctrine()->getRepository(Produit::class);
        $connexions = $repository->findEtablissementAndSpecialite($produit->getId());

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('produit/ajax_see_lien.html.twig', array(
                'produit' => $produit,
                'connexions' => $connexions
            ));
        }
    }
}
