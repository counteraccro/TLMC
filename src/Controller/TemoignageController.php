<?php
namespace App\Controller;

use App\Entity\Temoignage;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TemoignageType;

class TemoignageController extends AppController
{

    /**
     * Listing des témoiganges
     *
     * @Route("/temoignage/listing/{page}/{field}/{order}", name="temoignage_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Temoignage::class,
            'repository' => 'Temoignage',
            'repositoryMethode' => 'findAllTemoignages'
        );

        if ($this->isAdmin()) {
            $params['sans_inactif'] = false;
        } else {
            $membre = $this->getMembre();
            $params['condition'] = array(
                'key' => 'membre',
                'value' => $membre->getId()
            );
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
            'controller_name' => 'TemoignageController',
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
     * Ajout d'une nouveau témoignage
     *
     * @Route("/temoignage/add/{page}", name="temoignage_add")
     * @Route("/temoignage/ajax/add/{id}", name="temoignage_ajax_add")
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

        $temoignage = new Temoignage();

        if ($request->isXmlHttpRequest()) {
            
            $form = $this->createForm(TemoignageType::class, $temoignage, array(
                'submit' => 'Ajouter'
            ));
        } else {

            $form = $this->createForm(TemoignageType::class, $temoignage, array(
                'submit' => 'Ajouter'
            ));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            
            $temoignage->setMembre($this->getMembre());
            $temoignage->setDisabled(0);
            $temoignage->setDateCreation(new \DateTime());
            $em->persist($temoignage);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('temoignage_listing'));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('temoignage/ajax_add.html.twig', array(
                'form' => $form->createView(),
                //'membre' => $membre
            ));
        }

        return $this->render('temoignage/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('temoignage_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des témoignages'
                ),
                'active' => "Ajout d'un témoignage"
            )
        ));
    }

    /**
     * Désactivation d'un témoignage
     *
     * @Route("/temoignage/delete/{id}/{page}", name="temoignage_delete")
     * @ParamConverter("temoignage", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Temoignage $temoignage
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Temoignage $temoignage, $page = 1)
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
                'statut' => true
            ));
        }

        return $this->redirectToRoute('temoignage_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
