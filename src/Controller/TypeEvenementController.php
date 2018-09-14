<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\TypeEvenement;
use App\Form\TypeEvenementType;

class TypeEvenementController extends AppController
{

    /**
     * Listing des types d'événement
     *
     * @Route("/type_evenement/listing/{page}/{field}/{order}", name="type_evenement_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN')")
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
            'repositoryClass' => TypeEvenement::class,
            'repository' => 'TypeEvenement',
            'repositoryMethode' => 'findAllTypeEvenements'
        );

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'type_evenement_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('type_evenement/index.html.twig', array(
            'types' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des types d\'événement'
            )
        ));
    }

    /**
     * Ajout d'un type d'événement
     *
     * @Route("/type_evenement/add/{page}", name="type_evenement_add")
     * @Route("/type_evenement/ajax/add", name="type_evenement_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $type_evenement = new TypeEvenement();

        $form = $this->createForm(TypeEvenementType::class, $type_evenement, array(
            'label_submit' => 'Ajouter'
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $type_evenement->setDisabled(0);

            $em = $this->getDoctrine()->getManager();

            $em->persist($type_evenement);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true,
                    'id' => $type_evenement->getId(),
                    'nom' => $type_evenement->getNom()
                ));
            }
            
            return $this->redirect($this->generateUrl('type_evenement_listing'));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('type_evenement/ajax_add.html.twig', array(
                'form' => $form->createView()
            ));
        }

        return $this->render('type_evenement/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('type_evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion des types d'évenement"
                ),
                'active' => "Ajout d'un type d'événement"
            )
        ));
    }

    /**
     * Edition d'un type d'événement
     *
     * @Route("/type_evenement/edit/{id}/{page}", name="type_evenement_edit")
     * @ParamConverter("type_evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param TypeEvenement $type_evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, TypeEvenement $type_evenement, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(TypeEvenementType::class, $type_evenement, array(
            'label_submit' => 'Modifier'
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($type_evenement);
            $em->flush();

            return $this->redirect($this->generateUrl('type_evenement_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        return $this->render('type_evenement/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'type' => $type_evenement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('type_evenement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de types d\'événement'
                ),
                'active' => 'Edition de #' . $type_evenement->getId() . ' - ' . $type_evenement->getNom()
            )
        ));
    }

    /**
     * Désactivation d'un type d'événement
     *
     * @Route("/type_evenement/delete/{id}/{page}", name="type_evenement_delete")
     * @ParamConverter("evenement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param TypeEvenement $type_evenement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, TypeEvenement $type_evenement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($type_evenement->getDisabled() == 1) {
            $type_evenement->setDisabled(0);
        } else {
            $type_evenement->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($type_evenement);

        $entityManager->flush();

        return $this->redirectToRoute('type_evenement_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
