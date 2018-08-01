<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Specialite;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\SpecialiteType;
use App\Entity\Etablissement;

class SpecialiteController extends AppController
{

    /**
     * Listing des spécialités
     *
     * @Route("/specialite/listing/{page}/{field}/{order}", name="specialite_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN')")
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
            'repositoryClass' => Specialite::class,
            'repository' => 'Specielite',
            'repositoryMethode' => 'findAllSpecialites',
            'sans_inactif' => false
        );

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'specialite_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('specialite/index.html.twig', [
            'controller_name' => 'SpecialiteController',
            'specialites' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des spécialités'
            )
        ]);
    }

    /**
     * Fiche d'une spécialité
     *
     * @Route("/specialite/see/{id}/{page}", name="specialite_see")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function seeAction(SessionInterface $session, Specialite $specialite, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('specialite/see.html.twig', [
            'page' => $page,
            'specialite' => $specialite,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des spécialités'
                ),
                'active' => 'Fiche d\'une spécialité'
            )
        ]);
    }

    /**
     * Ajout d'une nouvelle spécialité
     *
     * @Route("/specialite/add/{page}", name="specialite_add")
     * @Route("/specialite/ajax/add/{id}", name="specialite_ajax_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $specialite = new Specialite();

        if ($request->isXmlHttpRequest()) {
            $repository = $this->getDoctrine()->getRepository(Etablissement::class);
            $result = $repository->findById($id);
            $etablissement = $result[0];
            $specialite->setPatient($etablissement);

            $form = $this->createForm(SpecialiteType::class, $specialite, array(
                'label_submit' => 'Ajouter',
                'disabled_etablissement' => true
            ));
        } else {

            $form = $this->createForm(SpecialiteType::class, $specialite, array(
                'label_submit' => 'Ajouter'
            ));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // TODO A changer
            $specialite->setDisabled(0);

            $em->persist($specialite);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('specialite_listing'));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('specialite/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'etablissement' => $etablissement
            ));
        }

        return $this->render('specialite/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des spécialités'
                ),
                'active' => "Ajout d'une spécialité"
            )
        ]);
    }

    /**
     * Edition d'une spécialité
     *
     * @Route("/specialite/edit/{id}/{page}", name="specialite_edit")
     * @ParamConverter("specialite", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editAction(SessionInterface $session, Request $request, Specialite $specialite, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(SpecialiteType::class, $specialite, array(
            'disabled_etablissement' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($specialite);
            $em->flush();

            return $this->redirect($this->generateUrl('specialite_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        return $this->render('specialite/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'specialite' => $specialite,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('specialite_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de spécialités'
                ),
                'active' => 'Edition de #' . $specialite->getId() . ' - ' . $specialite->getService()
            )
        ]);
    }

    /**
     * désactivation d'une spécialité
     *
     * @Route("/specialite/delete/{id}/{page}", name="specialite_delete")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(SessionInterface $session, Specialite $specialite, $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($specialite->getDisabled() == 1) {
            $specialite->setDisabled(0);
        } else {
            $specialite->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($specialite);

        $entityManager->flush();

        return $this->redirectToRoute('specialite_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
