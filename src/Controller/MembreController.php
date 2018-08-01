<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\MembreType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Etablissement;

class MembreController extends AppController
{

    /**
     * Listing des membres
     *
     * @Route("/membre/listing/{page}/{field}/{order}", name="membre_listing", defaults={"page" = 1, "field"= null, "order"= null})
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
            'repositoryClass' => Membre::class,
            'repository' => 'Membre',
            'repositoryMethode' => 'findAllMembres',
            'sans_inactif' => false
        );

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'membre_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('membre/index.html.twig', array(
            'controller_name' => 'MembreController',
            'membres' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Liste des membres'
            )
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'ajout d'un membre
     *
     * @Route("/membre/ajax/add/specialite/{id}", name="membre_ajax_add_specialite", defaults={"id" = 0})
     * @ParamConverter("etablissement", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Etablissement $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAjaxSpecialiteAction(Request $request, Etablissement $etablissement)
    {
        $specialites = $etablissement->getSpecialites();

        return $this->render('membre/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => 0
        ));
    }

    /**
     * Mise à jour du dropdown Spécialité lorsque l'établissement change dans le formulaire d'édition d'un membre
     *
     * @Route("/membre/ajax/edit/{membre_id}/{etablissement_id}", name="membre_ajax_edit_specialite", defaults={"etablissement_id" = 0})
     * @ParamConverter("membre", options={"mapping": {"membre_id": "id"}})
     * @ParamConverter("etablissement", options={"mapping": {"etablissement_id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Membre $membre
     * @param Etablissement $etablissement
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAjaxSpecialiteAction(Request $request, Membre $membre, Etablissement $etablissement = null)
    {
        $select_specialite = 0;
        if (! is_null($membre->getSpecialite())) {
            $select_specialite = $membre->getSpecialite()->getId();
        }

        if (! is_null($etablissement)) {
            $specialites = $etablissement->getSpecialites();
        } else {
            $specialites = $membre->getEtablissement()->getSpecialites();
        }

        return $this->render('membre/ajax_dropdown_specialite.html.twig', array(
            'specialites' => $specialites,
            'select_specialite' => $select_specialite
        ));
    }

    /**
     * Fiche d'un membre
     *
     * @Route("/membre/see/{id}/{page}", name="membre_see")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Membre $membre
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Membre $membre, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('membre/see.html.twig', array(
            'page' => $page,
            'membre' => $membre,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de membres'
                ),
                'active' => 'Fiche d\'un membre'
            )
        ));
    }

    /**
     * Ajout d'un nouveau membre
     *
     * @Route("/membre/add/{page}", name="membre_add")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(UserPasswordEncoderInterface $encoder, SessionInterface $session, Request $request, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $membre = new Membre();

        $form = $this->createForm(MembreType::class, $membre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $membre->setDisabled(0);
            $membre->setSalt($this->generateSalt());

            $encodePassword = $encoder->encodePassword($membre, $membre->getPassword());
            $membre->setPassword($encodePassword);
            $em->persist($membre);
            $em->flush();

            return $this->redirect($this->generateUrl('membre_listing'));
        }

        return $this->render('membre/add.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion des membres'
                ),
                'active' => "Ajout d'un membre"
            )
        ));
    }

    /**
     * Edition d'un membre
     *
     * @Route("/membre/edit/{id}/{page}", name="membre_edit")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Membre $membre
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, UserPasswordEncoderInterface $encoder, Membre $membre, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(MembreType::class, $membre, array(
            'edit' => true
        ));
        $password = $membre->getPassword();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if ($membre->getPassword() == 'password') {
                $membre->setPassword($password);
            } else {
                $encodePassword = $encoder->encodePassword($membre, $membre->getPassword());
                $membre->setPassword($encodePassword);
            }

            $em->persist($membre);
            $em->flush();

            return $this->redirect($this->generateUrl('membre_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        return $this->render('membre/edit.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
            'membre' => $membre,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de membres'
                ),
                'active' => 'Edition de #' . $membre->getId() . ' - ' . $membre->getPrenom() . ' ' . $membre->getNom()
            )
        ));
    }

    /**
     * Désactivation d'un membre
     *
     * @Route("/patient/delete/{id}/{page}", name="membre_delete")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Membre $membre
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SessionInterface $session, Membre $membre, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($membre->getDisabled() == 1) {
            $membre->setDisabled(0);
        } else {
            $membre->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($membre);

        $entityManager->flush();

        return $this->redirectToRoute('membre_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Génération automatique de salt
     */
    public function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
