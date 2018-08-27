<?php
namespace App\Controller;

use App\Entity\Temoignage;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TemoignageType;
use App\Entity\Membre;
use App\Entity\Evenement;
use App\Entity\Produit;
use App\Entity\Famille;

class TemoignageController extends AppController
{

    /**
     * Listing des témoiganges
     *
     * @Route("/temoignage/listing/{type}/{page}/{field}/{order}", name="temoignage_listing", defaults={"page" = 1, "type"="tous", "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param string $type
     * @param int $page
     * @param string $field
     * @param string $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, SessionInterface $session, string $type = 'tous', int $page = 1, $field = null, $order = null)
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
            'repositoryClass' => Temoignage::class,
            'repository' => 'Temoignage',
            'repositoryMethode' => 'findAllTemoignages'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();
            $params['condition'] = array(
                $params['repository'] . '.membre = ' . $membre->getId(),
                $params['repository'] . '.disabled = 0'
            );

            // récupération des témoignages liés à un événement ou un produit associé à la spécialité
            if ($membre->getSpecialite()) {
                $params['jointure'] = array(
                    array(
                        'oldrepository' => 'Temoignage',
                        'newrepository' => $type
                    ),
                    array(
                        'oldrepository' => $type,
                        'newrepository' => ($type == 'produit' ? 'produitSpecialites' : 'specialiteEvenements')
                    )
                );
                $params['condition'][] = ($type == 'produit' ? 'produitSpecialites' : 'specialiteEvenements') . '.specialite = ' . $membre->getSpecialite()->getId();
            } elseif ($type == 'produit') {
                $params['jointure'] = array(
                    array(
                        'oldrepository' => 'Temoignage',
                        'newrepository' => 'produit'
                    ),
                    array(
                        'oldrepository' => 'produit',
                        'newrepository' => 'produitEtablissements'
                    )
                );
                $params['condition'][] = 'produitEtablissements.etablissement = ' . $this->getMembre()
                    ->getEtablissement()
                    ->getId();
            }
        } else {
            $params['condition'] = array();
        }

        if ($type == 'evenement' || $type == 'produit') {
            $params['condition'][] = 'Temoignage.' . $type . ' IS NOT NULL';
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'temoignage_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('temoignage/index.html.twig', array(
            'type' => $type,
            'temoignages' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des témoignages'
            )
        ));
    }

    /**
     * Fiche d'un témoignage
     *
     * @Route("/temoignage/see/{id}/{type}/{page}", name="temoignage_see")
     * @Route("/temoignage/see/{id}", name="temoignage_ajax_see")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Temoignage $temoignage
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Temoignage $temoignage, int $page = 1, string $type = 'tous')
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('temoignage/ajax_see.html.twig', array(
                'temoignage' => $temoignage
            ));
        }

        return $this->render('temoignage/see.html.twig', array(
            'page' => $page,
            'type' => $type,
            'temoignage' => $temoignage,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'type' => $type,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de témoignages"
                ),
                'active' => 'Fiche d\'un témoignage'
            )
        ));
    }

    /**
     * Bloc témoignage d'un membre / d'un événement / d'un produit
     *
     * @Route("/temoignage/ajax/see/listing/{id}/{type}/{page}", name="temoignage_ajax_see_liste")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
     *
     * @param int $id
     * @param string $type
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type, int $page = 1)
    {
        switch ($type) {
            case 'membre':
                $repository = $this->getDoctrine()->getRepository(Membre::class);
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                break;
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                break;
        }

        $objets = $repository->findById($id);
        $objet = $objets[0];

        $params = array(
            'field' => 'date_creation',
            'order' => 'DESC',
            'page' => $page,
            'repositoryClass' => Temoignage::class,
            'repository' => 'Temoignage',
            'repositoryMethode' => 'findAllTemoignages',
        );
        
        $params['condition'] = array(
            $params['repository'] . '.' . $type . '  = ' . $id
        );
        
        if (! $this->isAdmin()) {
            $params['condition'][] = $params['repository'] . '.disabled = 0';
        }
        
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'temoignage_ajax_see_liste',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#ajax_' . $type . '_temoignage_see',
            'route_params' => array(
                'id' => $id,
                'type' => $type
            )
        );

        return $this->render('temoignage/ajax_see_list.html.twig', array(
            'objet' => $objet,
            'type' => $type,
            'temoignages' => $result['paginator'],
            'pagination' => $pagination
        ));
    }

    /**
     * Ajout d'une nouveau témoignage
     *
     * @Route("/temoignage/add/{page}/{type}", name="temoignage_add")
     * @Route("/temoignage/ajax/add/{id}/{type}", name="temoignage_ajax_add")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = 0, string $type = 'tous')
    {
        $arrayFilters = $this->getDatasFilter($session);
        $opt_form = array(
            'label_submit' => 'Ajouter'
        );

        $temoignage = new Temoignage();
        
        $membre = $this->getMembre();
        $id_specialite = (is_null($membre->getSpecialite()) ? 0 : $membre->getSpecialite()->getId());
        $isAdmin = $this->isAdmin();
        
        switch ($type) {
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);

                if ($request->isXmlHttpRequest()) {
                    $objets = $repository->findById($id);
                    $objet = $objets[0];

                    $opt_form['disabled_event'] = true;
                    $opt_form['required_event'] = true;
                    $opt_form['avec_prod'] = false;

                    $temoignage->setEvenement($objet);
                } else {
                    $opt_form['query_event'] = $repository->getEvenementAvailable($isAdmin, $id_specialite);
                    $opt_form['required_event'] = true;
                }
                break;
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);

                if ($request->isXmlHttpRequest()) {
                    $objets = $repository->findById($id);
                    $objet = $objets[0];

                    $opt_form['disabled_prod'] = true;
                    $opt_form['avec_event'] = false;

                    $temoignage->setProduit($objet);
                } else {
                    $opt_form['query_prod'] = $repository->getProduitAvailable($isAdmin, $membre->getEtablissement()->getId(), $id_specialite);
                    $opt_form['required_prod'] = true;
                }
                break;
        }

        $form = $this->createForm(TemoignageType::class, $temoignage, $opt_form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            //Pour associer la famille lors de l'ajout depuis une modale
            if ($request->isXmlHttpRequest()) {
                $famille_id = $request->request->get('temoignage')['famille'];
                $familles = $this->getDoctrine()->getRepository(Famille::class)->findById($famille_id);
                $famille = $familles[0];
                $temoignage->setFamille($famille);
            }
            
            $temoignage->setMembre($membre);
            $temoignage->setDisabled(0);
            $temoignage->setDateCreation(new \DateTime());
            $em->persist($temoignage);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('temoignage_listing', array(
                    'type' => $type
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('temoignage/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'objet' => $objet,
                'type' => $type
            ));
        }

        return $this->render('temoignage/add.html.twig', array(
            'page' => $page,
            'type' => $type,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'type' => $type,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des témoignages'
                ),
                'active' => "Ajout d'un témoignage"
            )
        ));
    }

    /**
     * Edition d'un témoignage
     *
     * @Route("/temoignage/edit/{id}/{page}/{type}", name="temoignage_edit")
     * @Route("/temoignage/ajax/edit/{id}/{type}/{page}", name="temoignage_ajax_edit")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Temoignage $temoignage
     * @param int $page
     * @param int $objet_id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Temoignage $temoignage, int $page = 1, string $type = 'tous')
    {
        $arrayFilters = $this->getDatasFilter($session);
    
        $opt_form = array(
            'label_submit' => 'Modifier'
        );

        if ($request->isXmlHttpRequest()) {
            switch ($type) {
                case 'evenement':
                    $opt_form['disabled_event'] = true;
                    $opt_form['avec_prod'] = false;
                    break;
                case 'produit':
                    $opt_form['disabled_prod'] = true;
                    $opt_form['avec_event'] = false;
                    break;
                case 'temoignage':
                case 'membre':
                    $opt_form['disabled_event'] = true;
                    $opt_form['disabled_prod'] = true;
                    break;
            }
        } else {
            // récupération du produit ou de l'événement associé
            if ($type == 'produit') {
                $produit = $temoignage->getProduit();
            } elseif ($type == 'evenement') {
                $evenement = $temoignage->getEvenement();
                $famille = $temoignage->getFamille();
            }
        }

        $form = $this->createForm(TemoignageType::class, $temoignage, $opt_form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if (! $request->isXmlHttpRequest()) {
                if ($type == 'produit') {
                    $temoignage->setProduit($produit);
                } elseif ($type == 'evenement') {
                    $temoignage->setEvenement($evenement);
                    $temoignage->setFamille($famille);
                }
            }

            $em->persist($temoignage);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true,
                    'page' => $page
                ));
            }

            return $this->redirect($this->generateUrl('temoignage_listing', array(
                'page' => $page,
                'type' => $type,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {
            return $this->render('temoignage/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'temoignage' => $temoignage,
                'type' => $type,
                'page' => $page
            ));
        }

        return $this->render('temoignage/edit.html.twig', array(
            'page' => $page,
            'type' => $type,
            'form' => $form->createView(),
            'temoignage' => $temoignage,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'type' => $type,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des témoignages'
                ),
                'active' => 'Edition de #' . $temoignage->getId() . ' - ' . $temoignage->getTitre()
            )
        ));
    }

    /**
     * Désactivation d'un témoignage
     *
     * @Route("/temoignage/delete/{id}/{type}/{page}", name="temoignage_delete")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Temoignage $temoignage
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Temoignage $temoignage, int $page = 1, string $type = 'tous')
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($temoignage->getDisabled() == 1) {
            $temoignage->setDisabled(0);
        } else {
            $temoignage->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($temoignage);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('temoignage_listing', array(
            'page' => $page,
            'type' => $type,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
