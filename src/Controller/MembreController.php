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
use App\Entity\Questionnaire;
use App\Entity\Groupe;
use App\Entity\GroupeMembre;
use Symfony\Component\Form\FormError;
use App\Form\AvatarType;
use App\Service\EmailManager;

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
            'repositoryMethode' => 'findAllMembres'
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
     * Fiche d'un membre
     *
     * @Route("/membre/see/{id}/{page}", name="membre_see")
     * @Route("/membre/ajax/see/{id}", name="membre_ajax_see")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Membre $membre
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(Request $request, SessionInterface $session, Membre $membre, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('membre/ajax_see.html.twig', array(
                'membre' => $membre
            ));
        }

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
     * Page "Mon compte"
     *
     * @Route("/membre/see_fiche", name="membre_see_fiche")
     * @Route("/membre/ajax/see_fiche", name="membre_ajax_see_fiche")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEVOLE')")
     *
     * @param Membre $membre
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeFicheAction(Request $request)
    {
        $membre = $this->getMembre();
     
        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('membre/ajax_see_fiche.html.twig', array(
                'membre' => $membre
            ));
        }
        

        
        $repository = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $repository->findByMembreReponses($membre->getId());

        return $this->render('membre/see_fiche.html.twig', array(
            'membre' => $membre,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => 'Mon compte'
            ),
            'questionnaires' => $questionnaires
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
     * @param EmailManager $sendEmail
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(UserPasswordEncoderInterface $encoder, SessionInterface $session, Request $request, int $page, EmailManager $sendEmail)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $membre = new Membre();

        $form = $this->createForm(MembreType::class, $membre);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $file = $form['avatar']->getData();
            if (! is_null($file)) {
                $fileName = $this->telechargerImage($file, 'membre', $membre->getPrenom() . '-' . $membre->getNom(), 'avatar');
                if ($fileName) {
                    $membre->setAvatar($fileName);
                } else {
                    $form->addError(new FormError("Le fichier n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
                }
            }

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $groupe = $this->getDoctrine()
                    ->getRepository(Groupe::class)
                    ->findOneBy(array(
                    'nom' => self::GROUPE_GLOBAL
                ));

                $groupe_membre = new GroupeMembre();
                $groupe_membre->setDate(new \DateTime());
                $groupe_membre->setGroupe($groupe);
                $groupe_membre->setMembre($membre);
                $em->persist($groupe_membre);

                $membre->setDisabled(0);
                $membre->setSalt($this->generateSalt());
                $password_en_clair = $membre->getPassword();
                $encodePassword = $encoder->encodePassword($membre, $membre->getPassword());
                $membre->setPassword($encodePassword);
                $em->persist($membre);
                $em->flush();

                $params = array(
                    'expediteur' => array(AppController::ADRESSE_ENVOI_MAIL_AUTO),
                    'destinataire' => array($membre->getEmail()),
                    'body' => $this->render('emails/registration.html.twig', [
                        'nom' => htmlentities($membre->getNom()),
                        'prenom' => htmlentities($membre->getPrenom()),
                        'username' => htmlentities($membre->getUsername()),
                        'password' => htmlentities($password_en_clair)
                    ]),
                );
                $sendEmail->send($params);
                
                //REDIRECTION VERS LA PAGE Membre mais pas possible de checker mail auto (voir *)
                return $this->redirect($this->generateUrl('membre_see', array(
                    'id' => $membre->getId()
                )));

                // (*) ce render permet de visualiser le mail auto de confirmation d'inscription
//                 return $this->render('membre/see.html.twig', array(
//                     'page' => $page,
//                     'membre' => $membre,
//                     'paths' => array(
//                         'home' => $this->indexUrlProject(),
//                         'urls' => array(
//                             $this->generateUrl('membre_listing', array(
//                                 'page' => $page,
//                                 'field' => $arrayFilters['field'],
//                                 'order' => $arrayFilters['order']
//                             )) => 'Gestion des membres'
//                         ),
//                         'active' => "Ajout d'un membre"
//                     )
//                 ));
            }
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
     * Edition de la fiche d'un membre
     *
     * @Route("/membre/ajax/edit_fiche/{id}", name="membre_ajax_edit_fiche")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Membre $membre
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editFicheAction(SessionInterface $session, Request $request, UserPasswordEncoderInterface $encoder, Membre $membre)
    {
        $form = $this->createForm(MembreType::class, $membre, array(
            'edit' => true,
            'disabled_etablissement' => true,
            'disabled_specialite' => true,
            'admin' => false,
            'ajax' => ($request->isXmlHttpRequest() ? true : false)
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

            return $this->json(array(
                'statut' => true
            ));
        }

        return $this->render('membre/ajax_edit.html.twig', [
            'membre' => $membre,
            'form' => $form->createView(),
            'admin' => false
        ]);
    }

    /**
     * Edition de l'avatar d'un membre
     *
     * @Route("/membre/edit_avatar/", name="membre_edit_avatar")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE_DIRECT') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEVOLE')")
     */
    public function editAvatarAction(Request $request, int $page = 1)
    {
        $membre = $this->getMembre();
        
        $form = $this->createForm(AvatarType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $file = $form['avatar']->getData();
            if (! is_null($file)) {
                $fileName = $this->telechargerImage($file, 'membre', $membre->getPrenom() . '-' . $membre->getNom(), 'avatar');
                if ($fileName) {
                    $membre->setAvatar($fileName);
                } else {
                    $form->addError(new FormError("Le fichier n'est pas au format autorisé (" . implode(', ', AppController::FORMAT_IMAGE) . ")."));
                }
            }
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($membre);
                $em->flush();

                return $this->redirect($this->generateUrl('membre_see_fiche', array(
                    'id' => $membre->getId()
                )));
            }
        }
        
        return $this->render('membre/edit_avatar.html.twig', [
            'membre' => $membre,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('membre_see_fiche', array(
                        'id' => $membre->getId()
                    )) => 'Mon compte'
                ),
                'active' => 'Modification avatar'
            )
        ]);
    }

    /**
     * Edition d'un membre
     *
     * @Route("/membre/edit/{id}/{page}", name="membre_edit")
     * @Route("/membre/ajax/edit/{id}", name="membre_ajax_edit")
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
    public function editAction(SessionInterface $session, Request $request, UserPasswordEncoderInterface $encoder, Membre $membre, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $avatar = $membre->getAvatar();

        $form = $this->createForm(MembreType::class, $membre, array(
            'edit' => true,
            'ajax' => ($request->isXmlHttpRequest() ? true : false)
        ));
        $password = $membre->getPassword();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (! $request->isXmlHttpRequest()) {
                if (is_null($membre->getAvatar()) && ! is_null($avatar)) {
                    $membre->setAvatar($avatar);
                } else {
                    $file = $request->files->get('membre')['avatar'];
                    $fileName = $this->telechargerImage($file, 'membre', $membre->getPrenom() . '-' . $membre->getNom(), 'avatar', $avatar);
                    if ($fileName) {
                        $membre->setAvatar($fileName);
                    } else {
                        $form->addError(new FormError("Le fichier n'est pas au format autorisé (jpg, jpeg,png)."));
                    }
                }
            }

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                if ($membre->getPassword() == 'password') {
                    $membre->setPassword($password);
                } else {
                    $encodePassword = $encoder->encodePassword($membre, $membre->getPassword());
                    $membre->setPassword($encodePassword);
                }

                $em->persist($membre);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json(array(
                        'statut' => true
                    ));
                }

                return $this->redirect($this->generateUrl('membre_listing', array(
                    'page' => $page,
                    'field' => $arrayFilters['field'],
                    'order' => $arrayFilters['order']
                )));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('membre/ajax_edit.html.twig', [
                'membre' => $membre,
                'form' => $form->createView(),
                'admin' => true
            ]);
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
     * @Route("/membre/delete/{id}/{page}", name="membre_delete")
     * @ParamConverter("membre", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param Membre $membre
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Membre $membre, int $page)
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

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true,
                'page' => $page
            ));
        }

        return $this->redirectToRoute('membre_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }

    /**
     * Fonction permettant la suggestion de destinataires d'un message (autocomplétion)
     * Gestion de l'homonymie (récupération des IDs plutôt que des prénoms/noms) et affichage de la fonction du membre si homonymes existants
     *
     * @Route("/membre/ajax/autocomplete", name="membre_ajax_autocomplete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * @param Request $request
     */
    public function autoCompleteAction(Request $request)
    {
        $term = $request->get('term');

        $repository = $this->getDoctrine()->getRepository(Membre::class);
        $result = $repository->findByTerm($term);

        // transformation de l'objet $result en array
        $membres = array();
        foreach ($result as $membre) {
            $membres[] = $membre->getPrenom() . ' ' . $membre->getNom();
        }

        $json = array();

        // renvoie les résultats à afficher
        foreach ($result as $membre) {
            if (array_count_values($membres)[$membre->getPrenom() . ' ' . $membre->getNom()] > 1) {
                $json[$membre->getId()] = $membre->getPrenom() . ' ' . $membre->getNom() . ' (' . $membre->getFonction() . ')';
            } else {
                $json[$membre->getId()] = $membre->getPrenom() . ' ' . $membre->getNom();
            }
        }
        return $this->json($json);
    }

    /**
     * Génération automatique de salt
     */
    public function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
}
