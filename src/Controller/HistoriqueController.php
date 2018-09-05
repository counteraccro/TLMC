<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Historique;
use App\Entity\Specialite;
use App\Entity\Evenement;
use App\Entity\Patient;
use App\Form\HistoriqueType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Membre;
use App\Entity\Participant;
use App\Entity\Famille;

class HistoriqueController extends AppController
{

    /**
     * Listing de l'historique
     *
     * @Route("/historique/listing/{page}/{field}/{order}", name="historique_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
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
            'field' => 'Historique' . $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => Historique::class,
            'repository' => 'Historique',
            'repositoryMethode' => 'findAllHistoriques'
        );

        if (! $this->isAdmin()) {
            $params['condition'] = array(
                $params['repository'] . 'membre = ' . $this->getUser()->getId()
            );
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'historique_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('historique/index.html.twig', array(
            'historiques' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des historiques'
            )
        ));
    }

    /**
     * Bloc historique
     *
     * @Route("/historique/ajax/see/{id}/{type}/{page}", name="historique_ajax_see")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param int $id
     * @param string type
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSeeAction(int $id, string $type, int $page = 1)
    {
        switch ($type) {
            case 'specialite':
                $repository = $this->getDoctrine()->getRepository(Specialite::class);
                $objet = $repository->findOneBy(array(
                    'id' => $id
                ));
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $objet = $repository->findOneBy(array(
                    'id' => $id
                ));
                break;
            case 'patient':
                $repository = $this->getDoctrine()->getRepository(Patient::class);
                $objet = $repository->findOneBy(array(
                    'id' => $id
                ));
                break;
            case 'membre':
                $repository = $this->getDoctrine()->getRepository(Membre::class);
                $objet = $repository->findOneBy(array(
                    'id' => $id
                ));
                break;
        }

        $params = array(
            'field' => 'evenement.date_debut DESC, evenement.nom',
            'order' => 'ASC',
            'page' => $page,
            'repositoryClass' => Historique::class,
            'repository' => 'Historique',
            'repositoryMethode' => 'findAllHistoriques',
            'condition' => array(
                'Historique.' . $type . ' = ' . $id
            ),
            'jointure' => array(
                array(
                    'oldrepository' => 'Historique',
                    'newrepository' => 'evenement'
                )
            )
        );

        if ($type != 'specialite' && ! $this->isAdmin()) {
            $specialite = $this->getMembre()->getSpecialite();
            $params['condition'][] = 'Historique.specialite = ' . (is_null($specialite) ? 0 : $specialite->getId());
        }

        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        $result = $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT_AJAX, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'historique_ajax_see',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_AJAX),
            'nb_elements' => $result['nb'],
            'id_div' => '#ajax_historique_see',
            'route_params' => array(
                'id' => $id,
                'type' => $type
            )
        );

        return $this->render('historique/ajax_see.html.twig', array(
            'objet' => $objet,
            'type' => $type,
            'historiques' => $result['paginator'],
            'pagination' => $pagination
        ));
    }

    /**
     * Ajout d'une nouvel historique
     *
     * @Route("/historique/add/{id}/{page}", name="historique_add")
     * @Route("/historique/ajax/add/{id}/{type}", name="historique_ajax_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int page
     * @param int $id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = 0, string $type = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $opt_form = array(
            'label_submit' => 'Ajouter'
        );

        $historique = new Historique();

        if ($request->isXmlHttpRequest()) {
            switch ($type) {
                case 'specialite':
                    $repository = $this->getDoctrine()->getRepository(Specialite::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $id
                    ));

                    // sélection uniquement des événements proposés à la spécialité
                    $opt_form['query_evenement'] = $this->getDoctrine()
                        ->getRepository(Evenement::class)
                        ->createQueryBuilder('event')
                        ->join('event.specialiteEvenements', 'SE')
                        ->andWhere('SE.specialite = :specialite')
                        ->setParameter('specialite', $id)
                        ->andWhere('event.disabled = 0')
                        ->orderBy('event.nom', 'ASC');

                    $opt_form['disabled_specialite'] = true;

                    $historique->setSpecialite($objet);
                    break;
                case 'evenement':
                    $repository = $this->getDoctrine()->getRepository(Evenement::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $id
                    ));

                    // sélection uniquement des spécialité dans lesquelles l'événement est proposé
                    $opt_form['query_specialite'] = $this->getDoctrine()
                        ->getRepository(Specialite::class)
                        ->createQueryBuilder('s')
                        ->join('s.etablissement', 'e')
                        ->join('s.specialiteEvenements', 'SE')
                        ->andWhere('SE.evenement = :evenement')
                        ->setParameter('evenement', $id)
                        ->andWhere('s.disabled = 0')
                        ->orderBy('e.nom', 'ASC')
                        ->addOrderBy('s.service', 'ASC');

                    $opt_form['disabled_evenement'] = true;

                    $historique->setEvenement($objet);
                    break;
                case 'patient':
                    $repository = $this->getDoctrine()->getRepository(Patient::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $id
                    ));

                    // sélection uniquement des événements proposés à la spécialité
                    $opt_form['query_evenement'] = $this->getDoctrine()
                        ->getRepository(Evenement::class)
                        ->createQueryBuilder('event')
                        ->join('event.specialiteEvenements', 'SE')
                        ->andWhere('SE.specialite = :specialite')
                        ->setParameter('specialite', $objet->getSpecialite())
                        ->andWhere('event.disabled = 0')
                        ->orderBy('event.nom', 'ASC');

                    $opt_form['disabled_specialite'] = true;
                    $opt_form['disabled_patient'] = true;

                    $historique->setPatient($objet);
                    $historique->setSpecialite($objet->getSpecialite());
                    break;
                case 'membre':
                    $objet = $this->getMembre();
                    break;
            }
        }

        $historique->setMembre($this->getMembre());

        $form = $this->createForm(HistoriqueType::class, $historique, $opt_form);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $familles = $request->request->get('familles');
            
            if(is_array($familles)){
                foreach ($familles as $famille_id) {
                    $participant = new Participant();
                    $famille = $this->getDoctrine()
                    ->getRepository(Famille::class)
                    ->findOneBy(array(
                        'id' => $famille_id
                    ));
                    
                    $participant->setDate(new \DateTime());
                    
                    $participants = $this->getDoctrine()
                    ->getRepository(Participant::class)
                    ->findBy(array(
                        'famille' => $famille,
                        'evenement' => $historique->getEvenement()
                    ));
                    
                    if (count($participants) == 0) {
                        $participant->setEvenement($historique->getEvenement());
                        $participant->setPatient($historique->getPatient());
                        $participant->setFamille($famille);
                        $participant->setStatut(1);
                        $em->persist($participant);
                    }
                }
            }

            $historiques = $this->getDoctrine()
                ->getRepository(Historique::class)
                ->findBy(array(
                'patient' => $historique->getPatient(),
                'evenement' => $historique->getEvenement(),
                'specialite' => $historique->getSpecialite()
            ));

            if (count($historiques) == 0) {
                $historique->setDate(new \DateTime());
                $em->persist($historique);
            }

            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }

            return $this->redirect($this->generateUrl('historique_listing'));
        }

        if ($request->isXmlHttpRequest()) {

            return $this->render('historique/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'objet' => $objet,
                'type' => $type
            ));
        }

        return $this->render('historique/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('historique_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des historiques'
                ),
                'active' => "Ajout d'un historique"
            )
        ));
    }

    /**
     * Edition d'un historique
     *
     * @Route("/historique/edit/{id}/{page}", name="historique_edit")
     * @Route("/historique/ajax/edit/{id}/{objet_id}/{type}", name="historique_ajax_edit")
     * @ParamConverter("historique", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Historique $historique
     * @param int $page
     * @param int $objet_id
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Historique $historique, int $page = 1, int $objet_id = 0, string $type = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $opt_form = array(
            'label_submit' => 'Modifier'
        );

        if ($request->isXmlHttpRequest()) {
            switch ($type) {
                case 'evenement':
                    $repository = $this->getDoctrine()->getRepository(Evenement::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $objet_id
                    ));

                    $opt_form['disabled_evenement'] = true;
                    break;
                case 'specialite':
                    $repository = $this->getDoctrine()->getRepository(Specialite::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $objet_id
                    ));

                    $opt_form['disabled_specialite'] = true;
                    break;
                case 'patient':
                    $repository = $this->getDoctrine()->getRepository(Patient::class);
                    $objet = $repository->findOneBy(array(
                        'id' => $objet_id
                    ));

                    $opt_form['disabled_patient'] = true;
                    break;
                case 'membre':
                    $objet = $this->getMembre();
                    break;
            }
        }

        $form = $this->createForm(HistoriqueType::class, $historique, $opt_form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($historique);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }

            return $this->redirect($this->generateUrl('historique_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('historique/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'objet' => $objet,
                'historique' => $historique,
                'type' => $type
            ));
        }

        return $this->render('historique/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'historique' => $historique,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('historique_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de l'historique"
                ),
                'active' => 'Edition de #' . $historique->getId()
            )
        ));
    }
}
