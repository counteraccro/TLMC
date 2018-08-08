<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Etablissement;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EtablissementType;

class EtablissementController extends AppController
{

    /**
     * Listing des établissements
     *
     * @Route("/etablissement/listing/{page}/{field}/{order}", name="etablissement_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Etablissement::class,
            'repository' => 'Etablissement',
            'repositoryMethode' => 'findAllEtablissements'
        );

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'etablissement_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('etablissement/index.html.twig', array(
            'etablissements' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des établissements'
            )
        ));
    }

    /**
     * Fiche d'un établissement
     *
     * @Route("/etablissement/see/{id}/{page}", name="etablissement_see")
     * @Route("/etablissement/ajax/see/{id}", name="etablissement_ajax_see")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     * 
     * @param Request $request
     * @param SessionInterface $session
     * @param Etablissement $etablissement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Etablissement $etablissement, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {
            
            return $this->render('etablissement/ajax_see.html.twig', array(
                'etablissement' => $etablissement,
            ));
        }
        
        return $this->render('etablissement/see.html.twig', array(
            'page' => $page,
            'etablissement' => $etablissement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion d'établissements"
                ),
                'active' => 'Fiche d\'un établissement'
            )
        ));
    }

    /**
     * Ajout d'un nouvel établissement
     *
     * @Route("/etablissement/add/{page}", name="etablissement_add")
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

        $etablissement = new Etablissement();

        $form = $this->createForm(EtablissementType::class, $etablissement);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $etablissement->setDisabled(0);

            $em->persist($etablissement);
            $em->flush();

            return $this->redirect($this->generateUrl('etablissement_listing'));
        }

        return $this->render('etablissement/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des établissements'
                ),
                'active' => "Ajout d'un établissement"
            )
        ));
    }

    /**
     * Edition d'un établissement
     *
     * @Route("/etablissement/edit/{id}/{page}", name="etablissement_edit")
     * @Route("/etablissement/ajax/edit/{id}", name="etablissement_ajax_edit")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Etablissement $etablissement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Etablissement $etablissement, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(EtablissementType::class, $etablissement);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($etablissement);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }
            
            return $this->redirect($this->generateUrl('etablissement_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {
            
            return $this->render('etablissement/ajax_edit.html.twig', [
                'etablissement' => $etablissement,
                'form' => $form->createView()
            ]);
        }
        
        return $this->render('etablissement/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'etablissement' => $etablissement,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('etablissement_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de établissements'
                ),
                'active' => 'Edition de #' . $etablissement->getId() . ' - ' . $etablissement->getNom()
            )
        ));
    }

    /**
     * Désactivation d'un établissement
     *
     * @Route("/etablissement/delete/{id}/{page}", name="etablissement_delete")
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Etablissement $etablissement
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Etablissement $etablissement, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($etablissement->getDisabled() == 1) {
            $etablissement->setDisabled(0);
        } else {
            $etablissement->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($etablissement);

        $entityManager->flush();

        return $this->redirectToRoute('etablissement_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
