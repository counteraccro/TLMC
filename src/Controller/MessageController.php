<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\MessageLu;
use App\Form\MessageType;
use App\Entity\Groupe;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MessageController extends AppController
{

    const MAX_NB_RESULT_MESSAGERIE = 16;

    /**
     * Route de base nécessaire vers la messagerie (hors appels ajax)
     *
     * @Route("/messagerie", name="message")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function index()
    {
        return $this->render('message/index.html.twig', []);
    }

    /**
     * Récupère le nombre de message non-lus
     *
     * @Route("/messagerie/ajax/messageread", name="message_ajax_message_read")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxNbMessageNoRead()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $nbMessage = $repository->nbMessageNoRead($this->getUser()
            ->getId());

        return $this->json(array(
            'nb_message' => $nbMessage
        ));
    }

    /**
     * Affiche la liste des messages destinataire du user courant
     *
     * @Route("/messagerie/ajax/messagesdestinataire/{page}", name="message_ajax_messages_destinataire")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesDestinataire($page = 1)
    {
        $search = '';

        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findByUserByParameter($this->getUser()
            ->getId(), 0, 'destinataire', $page, self::MAX_NB_RESULT_MESSAGERIE, $search);

        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_destinataire',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_MESSAGERIE),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-reception'
        );

        return $this->render('message/ajax_messages_destinataire.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
            'page' => $page
        ]);
    }

    /**
     * Affiche les messages en brouillon du user courant
     *
     * @Route("/messagerie/ajax/messagesbrouillons/{page}", name="message_ajax_messages_brouillons")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesBrouillons(SessionInterface $session, $page = 1)
    {
        $search = '';

        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findByUserByParameter($this->getUser()
            ->getId(), 1, 'expediteur', $page, self::MAX_NB_RESULT_MESSAGERIE, $search);

        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_brouillons',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_MESSAGERIE),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-brouillons'
        );
        
        // Utilisé quand on met à jour un brouillon pour le charger correctement dans la vue
        $currentBrouillon = $session->get('Message.brouillon.id', 0);
        $session->remove('Message.brouillon.id');

        return $this->render('message/ajax_messages_brouillons.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
            'currentBrouillon' => $currentBrouillon,
            'page' => $page
        ]);
    }

    /**
     * Affiche les messages placés en corbeille du user courant
     *
     * @Route("/messagerie/ajax/messagescorbeille/{page}", name="message_ajax_messages_corbeille")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesCorbeille($page = 1)
    {
        $search = '';

        $repository = $this->getDoctrine()->getRepository(Message::class);

        $result = $repository->findCorbeilleByUser($this->getUser()
            ->getId(), $page, self::MAX_NB_RESULT_MESSAGERIE, $search);

        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_corbeille',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_MESSAGERIE),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-corbeille'
        );

        return $this->render('message/ajax_messages_corbeille.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator']
        ]);
    }

    /**
     * Affiche les messages envoyés par le user courant
     *
     * @Route("/messagerie/ajax/messagesenvoyes/{page}", name="message_ajax_messages_envoyes")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesEnvoyes($page = 1)
    {
        $search = '';

        $repository = $this->getDoctrine()->getRepository(Message::class);

        $result = $repository->findByUserByParameter($this->getUser()
            ->getId(), 0, 'expediteur', $page, self::MAX_NB_RESULT_MESSAGERIE, $search);

        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_envoyes',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_MESSAGERIE),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-envoyes'
        );

        return $this->render('message/ajax_messages_envoyes.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator']
        ]);
    }

    /**
     * Affiche le message envoyé en id (visualisation du message selectionné)
     *
     * @Route("/messagerie/ajax/viewmessage/{id}/{page}", name="message_ajax_view_message")
     * @ParamConverter("message", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxViewMessage(Message $message, $page = 1)
    {
        foreach ($message->getMessageLus() as &$messageLu) {
            if ($messageLu->getMembre()->getId() == $this->getUser()->getId()) {
                $messageLu->setLu(1);
                $messageLu->setDate(new \DateTime());
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->persist($messageLu);
        $em->flush();

        return $this->render('message/ajax_view_message.html.twig', [
            'message' => $message,
            'page' => $page
        ]);
    }

    /**
     * Fonction permettant les changements au clic sur "Marquer comme Lu/ Marquer comme non-lu"
     *
     * @Route("/messagerie/ajax/readnoread", name="message_ajax_read_no_read")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxMessageReadNoRead(Request $request)
    {
        $tab = $request->request->all();

        if (empty($tab['data'])) {
            return $this->json(array(
                'statut' => 1
            ));
        }

        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findById($tab['data']);

        foreach ($result as &$message) {
            foreach ($message->getMessageLus() as &$messageLu) {
                if ($messageLu->getMembre()->getId() == $this->getUser()->getId()) {
                    $messageLu->setLu($tab['isRead']);
                    $messageLu->setDate(new \DateTime());
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($messageLu);
            $em->persist($message);
        }
        $em->flush();

        return $this->json(array(
            'statut' => 1
        ));
    }

    /**
     * Fonction permettant l'envoi d'un ou plusieurs éléments en "Corbeille" (au clic sur bouton Corbeille)
     *
     * @Route("/messagerie/ajax/corbeille", name="message_ajax_corbeille")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function ajaxMessageCorbeille(Request $request)
    {
        $tab = $request->request->all();

        if (empty($tab['data'])) {
            return $this->json(array(
                'statut' => 1
            ));
        }

        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findById($tab['data']);

        foreach ($result as &$message) 
            /*@ var MessageLu $messageLu */{
            foreach ($message->getMessageLus() as &$messageLu) {
                if ($messageLu->getMembre()->getId() == $this->getUser()->getId()) {

                    $messageLu->setCorbeille($tab['corbeille']);
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($messageLu);
            $em->persist($message);
        }
        $em->flush();

        return $this->json(array(
            'statut' => 1
        ));
    }
    
    /**
     * Fonction permettant d'écrire un nouveau message ou brouillon
     *
     * @Route("/messagerie/ajax/newmessage/{page}", name="message_ajax_new_message")
     * @Route("/messagerie/ajax/editmessage/{id}/{brouillon}/{page}", name="message_ajax_edit_message")
     * @ParamConverter("message", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * @param Request $request
     */
    public function ajaxFormMessage(SessionInterface $session, Request $request, Message $message = null, $brouillon = 1, $page = 1)
    {
        $currentRoute = $request->attributes->get('_route');
        $membre = $this->getMembre();
        $em = $this->getDoctrine()->getManager();
        
        //lors de la création d'un nouveau message, enregistrement automatique en tant que brouillon (sauvegarde des données)
        if ($currentRoute == 'message_ajax_new_message') {
            
            $groupeRepository = $this->getDoctrine()->getRepository(Groupe::class);
            
            $message = new Message();
            $message->setGroupe($groupeRepository->findByNom(AppController::GROUPE_GLOBAL)[0]);
            $message->setExpediteur($membre);
            $message->setDestinataire($membre);
            $message->setBrouillon($brouillon);
            $message->setTitre('');
            $message->setCorps('');
            $message->setDateEnvoi(new \DateTime());
            $message->setDisabled(0);
            
            $messageLu = new MessageLu();
            $messageLu->setLu(0);
            $messageLu->setMembre($membre);
            $messageLu->setMessage($message);
            
            $message->addMessageLus($messageLu);
            
            $em->persist($messageLu);
            $em->persist($message);
            $em->flush();
        }
        
        $form = $this->createForm(MessageType::class, $message, array());
        
        $form->handleRequest($request);
        
        $erreur = false;
        $str_erreur_destinataire = '';
        $str_erreur_corps = '';
        if ($form->isSubmitted() && $form->isValid()) {
             
            $destinataires = explode('-', $request->request->all()['destinataire']['destinataire']);
            
            $destinataire = $membre;
            if($destinataires[0] != "")
            {
                $membreRepository = $this->getDoctrine()->getRepository(Membre::class);
                $destinataire = $membreRepository->findById($destinataires[0])[0];
            }
            
            if($brouillon == 0)
            {
                
                if($destinataire->getId() == $membre->getId())
                {
                    $erreur = true;
                    $str_erreur_destinataire = 'Vous devez saisir au moins un destinataire avant l\'envoi';
                }
                
                if((is_null($message->getCorps()) || $message->getCorps() == "" || $message->getCorps() == "-"))
                {
                    $erreur = true;
                    $str_erreur_corps = 'Vous devez saisir le corps du message avant l\'envoi';
                }
                
            }
            
            $message->setDestinataire($destinataire);
            $message->setDateEnvoi(new \DateTime());
            $message->setBrouillon($brouillon);
            
            //$em->persist($messageLu);
            //$em->persist($message);
            //$em->flush();
            
        }
        
        $session->set('Message.brouillon.id', $message->getId());
        
        $destinataire_input = '';
        $destinataire_input_id = '';
        if($message->getDestinataire()->getId() != $this->getUser()->getId())
        {
            $destinataire_input = $message->getDestinataire()->getPrenom() . ' ' . $message->getDestinataire()->getNom();
            $destinataire_input_id = $message->getDestinataire()->getId();
        }
        
        return $this->render('message/ajax_form_message.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'page' => $page,
            'destinataire' => $destinataire_input,
            'destinataire_id' => $destinataire_input_id,
            'membre' => $membre,
            'str_erreur_destinataire' => $str_erreur_destinataire,
            'str_erreur_corps' => $str_erreur_corps
        ]);
    }

}
