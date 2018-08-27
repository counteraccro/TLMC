<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Message;

class MessageController extends AppController
{

    /**
     * Route de base nécessaire vers la messagerie (hors appels ajax)
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
            ->getId(), 0, 'destinataire', $page, AppController::MAX_NB_RESULT, $search);

        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_destinataire',
            'pages_count' => ceil($result['nb'] / AppController::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-reception'
        );
        
        return $this->render('message/ajax_messages_destinataire.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
        ]);
    }

    /**
     * Affiche les messages en brouillon du user courant
     *
     * @Route("/messagerie/ajax/messagesbrouillons/{page}", name="message_ajax_messages_brouillons")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesBrouillons($page = 1)
    {
        $search = '';
        
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findByUserByParameter($this->getUser()
            ->getId(), 1, 'expediteur', $page, AppController::MAX_NB_RESULT, $search);
        
        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_brouillons',
            'pages_count' => ceil($result['nb'] / AppController::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-brouillons'
        );
        
        return $this->render('message/ajax_messages_brouillons.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
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
            ->getId(), $page, AppController::MAX_NB_RESULT, $search);
        
        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_corbeille',
            'pages_count' => ceil($result['nb'] / AppController::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-corbeille'
        );

        return $this->render('message/ajax_messages_corbeille.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
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
            ->getId(), 0, 'expediteur', $page, AppController::MAX_NB_RESULT, $search);
        
        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_envoyes',
            'pages_count' => ceil($result['nb'] / AppController::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array(),
            'id_div' => '#v-pills-envoyes'
        );
        
        return $this->render('message/ajax_messages_envoyes.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            'messages' => $result['paginator'],
        ]);
    }
}
