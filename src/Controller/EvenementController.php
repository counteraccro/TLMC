<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\EvenementType;
use App\Entity\Specialite;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Session\Session;

class EvenementController extends AppController
{

    /**
     * Tableau de statuts d'événements
     *
     * @var array
     */
    const STATUT = array(
        1 => 'Ouvert',
        2 => 'Inscription',
        3 => 'Annulé',
        4 => 'Fermé'
    );

    /**
     * Listing des événements.
     * Pour un membre non administrateur,
     * seul les événements actifs liés à la spécialité du membre sont affichés
     *
     * @Route("/evenement/listing/{page}/{field}/{order}", name="evenement_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
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
            'repositoryClass' => Evenement::class,
            'repository' => 'Evenement',
            'repositoryMethode' => 'findAllEvenements'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();

            $id_specialite = (! is_null($membre->getSpecialite()) ? $membre->getSpecialite()->getId() : '0');
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0',
                'specialiteEvenements.specialite = ' . $id_specialite
            );

            $params['jointure'] = array(
                array(
                    'oldrepository' => 'Evenement',
                    'newrepository' => 'specialiteEvenements'
                )
            );
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'evenement_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('evenement/index.html.twig', array(
            'evenements' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des événements'
            )
        ));
    }

    /**
     * Fiche d'un événement
     *
     * @Route("/evenement/see/{id}/{page}", name="evenement_see")
     * @Route("/evenement/ajax/see/{id}", name="evenement_ajax_see")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEVOLE')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Evenement $evenement, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('evenement/ajax_see.html.twig', array(
                'evenement' => $evenement
            ));
        }

        return $this->render('evenement/see.html.twig', array(
            'page' => $page,
            'evenement' => $evenement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'événements"
                ),
                'active' => 'Fiche d\'un événement'
            )
        ));
    }

    /**
     * Ajout d'un nouvel événement
     *
     * @Route("/evenement/add/{page}", name="evenement_add")
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

        $evenement = new Evenement();

        // requête pour le champ spécialité
        $membre = $this->getMembre();
        $sr = $this->getDoctrine()->getRepository(Specialite::class);
        $query = $sr->createQueryBuilder('specialite')->innerJoin('specialite.etablissement', 'etablissement');
        if ($membre->getSpecialite() && ! $this->isAdmin()) {
            $query->andWhere("etablissement.id = " . $membre->getEtablissement()
                ->getId());
        }
        $query->orderBy('etablissement.nom, specialite.service', 'ASC');

        $form = $this->createForm(EvenementType::class, $evenement, array(
            'query_specialite' => $query,
            'add' => true
        ));
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            // téléchargement des images
            for ($i = 1; $i <= 3; $i ++) {
                $file = $request->files->get('evenement')['image_' . $i];
                if (! is_null($file)) {
                    $fileName = $this->telechargerImage($file, 'evenement', $evenement->getNom(), 'image_' . $i);
                    if ($fileName) {
                        $methode = 'setImage' . $i;
                        $evenement->{$methode}($fileName);
                    } else {
                        $form->addError(new FormError("L'image $i n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
                    }
                }
            }

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                foreach ($evenement->getSpecialiteEvenements() as $specialiteEvenement) {
                    $specialiteEvenement->setEvenement($evenement);
                    $specialiteEvenement->setDate(new \DateTime());
                }

                $index = 1;
                foreach ($evenement->getExtensionFormulaires() as $extension) {
                    $extension->setOrdre($index);
                    $extension->setDisabled(0);
                    $extension->setEvenement($evenement);
                    $index ++;
                }

                $evenement->setDisabled(0);

                $em->persist($evenement);
                $em->flush();

                return $this->redirect($this->generateUrl('evenement_see', array(
                    'id' => $evenement->getId()
                )));
            }
        }

        return $this->render('evenement/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des événements'
                ),
                'active' => "Ajout d'un événement"
            )
        ));
    }

    /**
     * Edition d'un événement
     *
     * @Route("/evenement/edit/{id}/{page}", name="evenement_edit")
     * @Route("/evenement/ajax/edit/{id}", name="evenement_ajax_edit")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Evenement $evenement, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);
        $images = array(
            1 => $evenement->getImage1(),
            2 => $evenement->getImage2(),
            3 => $evenement->getImage3()
        );

        $form = $this->createForm(EvenementType::class, $evenement, array(
            'ajax' => ($request->isXmlHttpRequest() ? true : false)
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // traitement des images
            if (! $request->isXmlHttpRequest()) {
                for ($i = 1; $i <= 3; $i ++) {
                    $methodeGet = 'getImage' . $i;
                    $methodeSet = 'setImage' . $i;
                    if (is_null($evenement->{$methodeGet}()) && ! is_null($images[$i])) {
                        $evenement->{$methodeSet}($images[$i]);
                    } elseif (! is_null($evenement->{$methodeGet}())) {
                        $file = $request->files->get('evenement')['image_' . $i];
                        $fileName = $this->telechargerImage($file, 'evenement', $evenement->getNom(), 'image_'.$i, $images[$i]);
                        if ($fileName) {
                            $evenement->{$methodeSet}($fileName);
                        } else {
                            $form->addError(new FormError("L'image $i n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
                        }
                    }
                }
            }

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $index = 1;
                foreach ($evenement->getExtensionFormulaires() as $extension) {
                    if (is_null($extension->getEvenement())) {
                        $extension->setOrdre($index);
                        $extension->setDisabled(0);
                        $extension->setEvenement($evenement);
                    }
                    $index ++;
                }

                $tranche_age = $request->request->get('evenement')['tranche_age'];
                asort($tranche_age);
                $evenement->setTrancheAge($tranche_age);

                $em->persist($evenement);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json(array(
                        'statut' => true
                    ));
                }

                return $this->redirect($this->generateUrl('evenement_listing', array(
                    'page' => $page,
                    'field' => $arrayFilters['field'],
                    'order' => $arrayFilters['order']
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('evenement/ajax_edit.html.twig', array(
                'evenement' => $evenement,
                'form' => $form->createView()
            ));
        }

        return $this->render('evenement/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'evenement' => $evenement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de événements'
                ),
                'active' => 'Edition de #' . $evenement->getId() . ' - ' . $evenement->getNom()
            )
        ));
    }

    /**
     * Désactivation d'un événement
     *
     * @Route("/evenement/delete/{id}/{page}", name="evenement_delete")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Evenement $evenement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($evenement->getDisabled() == 1) {
            $evenement->setDisabled(0);
        } else {
            $evenement->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($evenement);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('evenement_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
    
    /**
     * Duplication d'un événement
     *
     * @Route("/evenement/duplicate/{id}/{page}", name="evenement_duplicate")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     * 
     * @param Request $request
     * @param Session $session
     * @param Evenement $evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function duplicateAction(Request $request, Session $session, Evenement $evenement, int $page = 1){
        
        $arrayFilters = $this->getDatasFilter($session);
        
        $evenementDuplicated = new Evenement();
        $evenementDuplicated->duplicate($evenement);
        
        // requête pour le champ spécialité
        $membre = $this->getMembre();
        $sr = $this->getDoctrine()->getRepository(Specialite::class);
        $query = $sr->createQueryBuilder('specialite')->innerJoin('specialite.etablissement', 'etablissement');
        if ($membre->getSpecialite() && ! $this->isAdmin()) {
            $query->andWhere("etablissement.id = " . $membre->getEtablissement()
                ->getId());
        }
        $query->orderBy('etablissement.nom, specialite.service', 'ASC');
        
        $form = $this->createForm(EvenementType::class, $evenementDuplicated, array(
            'query_specialite' => $query,
            'add' => true
        ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            
            // téléchargement des images
            for ($i = 1; $i <= 3; $i ++) {
                $file = $request->files->get('evenement')['image_' . $i];
                if (! is_null($file)) {
                    $fileName = $this->telechargerImage($file, 'evenement', $evenementDuplicated->getNom(), 'image_' . $i);
                    if ($fileName) {
                        $methode = 'setImage' . $i;
                        $evenementDuplicated->{$methode}($fileName);
                    } else {
                        $form->addError(new FormError("L'image $i n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
                    }
                }
            }
            
            if ($form->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                
                foreach ($evenementDuplicated->getSpecialiteEvenements() as $specialiteEvenement) {
                    $specialiteEvenement->setEvenement($evenementDuplicated);
                    $specialiteEvenement->setDate(new \DateTime());
                }
                
                foreach ($evenementDuplicated->getExtensionFormulaires() as $extension) {
                    $extension->setDisabled(0);
                    $extension->setEvenement($evenementDuplicated);
                }
                
                $evenementDuplicated->setDisabled(0);
                
                $em->persist($evenementDuplicated);
                $em->flush();
                
                return $this->redirect($this->generateUrl('evenement_see', array(
                    'id' => $evenementDuplicated->getId()
                )));
            }
        }
        
        return $this->render('evenement/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des événements'
                ),
                'active' => "Ajout d'un événement"
            )
        ));
    }
}
