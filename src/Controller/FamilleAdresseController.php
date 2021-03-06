<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\FamilleAdresse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\FamilleAdresseType;

class FamilleAdresseController extends AppController
{

    /**
     * Listing des adresses des familles
     *
     * @Route("/famille_adresse/listing/{page}/{field}/{order}", name="famille_adresse_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
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
            'repositoryClass' => FamilleAdresse::class,
            'repository' => 'FamilleAdresse',
            'repositoryMethode' => 'findAllFamilleAdresses'
        );

        if (! $this->isAdmin()) {
            $membre = $this->getMembre();

            $id_specialite = (! is_null($membre->getSpecialite()) ? $membre->getSpecialite()->getId() : '0');
            $params['condition'] = array(
                $params['repository'] . '.disabled = 0',
                'patient.specialite = ' . $id_specialite
            );

            $params['jointure'] = array(
                array(
                    'oldrepository' => 'FamilleAdresse',
                    'newrepository' => 'familles'
                ),
                array(
                    'oldrepository' => 'familles',
                    'newrepository' => 'patient'
                )
            );
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'famille_adresse_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('famille_adresse/index.html.twig', array(
            'adresses' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste d'adresses de famille"
            )
        ));
    }

    /**
     * Affichage de la fiche d'une adresse
     *
     * @Route("/famille_adresse/see/{id}/{page}", name="famille_adresse_see")
     * @Route("/famille_adresse/see/{id}", name="famille_adresse_ajax_see")
     * @ParamConverter("famille_adresse", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param FamilleAdresse $adresse
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, FamilleAdresse $adresse, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {
            
            return $this->render('famille_adresse/ajax_see.html.twig', array(
                'adresse' => $adresse
            ));
        }
        
        return $this->render('famille_adresse/see.html.twig', array(
            'page' => $page,
            'adresse' => $adresse,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_adresse_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'adresses"
                ),
                'active' => "Fiche d'une adresse"
            )
        ));
    }

    /**
     * Ajout d'une adresse
     *
     * @Route("/famille_adresse/add/{page}", name="famille_adresse_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $adresse = new FamilleAdresse();

        $form = $this->createForm(FamilleAdresseType::class, $adresse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setDisabled(0);

            $em = $this->getDoctrine()->getManager();

            $em->persist($adresse);
            $em->flush();

            return $this->redirect($this->generateUrl('famille_adresse_listing'));
        }

        return $this->render('famille_adresse/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_adresse_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'adresses"
                ),
                'active' => "Ajout d'une adresse"
            )
        ));
    }

    /**
     * Edition d'une adresse pour un membre d'une famille
     *
     * @Route("/famille_adresse/edit/{id}/{page}", name="famille_adresse_edit")
     * @Route("/famille_adresse/ajax/edit/{id}/{page}", name="famille_adresse_ajax_edit")
     * @ParamConverter("famille_adresse", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param FamilleAdresse $adresse
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, FamilleAdresse $adresse, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(FamilleAdresseType::class, $adresse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($adresse);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true,
                    'page' => $page
                ));
            }

            return $this->redirect($this->generateUrl('famille_adresse_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('famille_adresse/ajax_edit.html.twig', array(
                'form' => $form->createView(),
                'adresse' => $adresse,
                'page' => $page
            ));
        }

        return $this->render('famille_adresse/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_adresse_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de patients'
                ),
                'active' => 'Edition de #' . $adresse->getId()
            )
        ));
    }

    /**
     * Désactivation d'une adresse famille
     *
     * @Route("/famille_adresse/delete/{id}/{page}", name="famille_adresse_delete")
     * @ParamConverter("famille_adresse", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param FamilleAdresse $adresse
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, FamilleAdresse $adresse, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($adresse->getDisabled() == 1) {
            $adresse->setDisabled(0);
        } else {
            $adresse->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($adresse);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }
        
        return $this->redirectToRoute('famille_adresse_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
