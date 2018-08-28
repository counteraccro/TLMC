<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\MessageLu;

class MessageController extends AppController
{
    const MAX_NB_RESULT_MESSAGERIE = 50;
    

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
            ->getId(), 1, 'expediteur', $page, self::MAX_NB_RESULT_MESSAGERIE, $search);
        
        $pagination = array(
            'page' => $page,
            'route' => 'message_ajax_messages_brouillons',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT_MESSAGERIE),
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
            'messages' => $result['paginator'],
        ]);
    }
    
    /**
     * Affiche le message envoyé en id (visualisation du message selectionné)
     *
     * @Route("/messagerie/ajax/viewmessage/{id}", name="message_ajax_view_message")
     * @ParamConverter("message", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxViewMessage(Message $message)
    {
        return $this->render('message/ajax_view_message.html.twig', [
            'message' => $message,
        ]);
    }
    
    /**
     * A écrire
     *
     * @Route("/messagerie/ajax/readnoread", name="message_ajax_read_no_read")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessageReadNoRead(Request $request)
    {
        $tab = $request->request->all();
        
        $repository = $this->getDoctrine()->getRepository(Message::class);
        $result = $repository->findById($tab['data']);
        
        /* @var Message $message */
        /* @var MessageLu $messageLu */
        
        $ok = false;
        foreach ($result as &$message)
        {
            // Cas car les fixtures ne sont pas bonnes, en principe improbable
            if($message->getMessageLus()->isEmpty())
            {
                $messageLu = new MessageLu();
                $messageLu->setMembre($this->getMembre());
                $messageLu->setLu($tab['isRead']);
                $messageLu->setDate(new \DateTime());
                $messageLu->setMessage($message);
                $message->addMessageLus($messageLu);
                $ok = true;
            }
            else
            {
                foreach($message->getMessageLus() as &$messageLu)
                {
                    if($messageLu->getMembre()->getId() == $this->getMembre()->getId())
                    {
                        $messageLu->setLu($tab['isRead']);
                        $messageLu->setDate(new \DateTime());
                        $ok = true;
                    }
                }
            }
            
            // Cas car les fixtures ne sont pas bonnes, en principe improbable car soit pas de messageLu ou messageLu non 
            // existant pour le membre courant
            if(!$ok)
            {
                $messageLu = new MessageLu();
                $messageLu->setMembre($this->getMembre());
                $messageLu->setLu($tab['isRead']);
                $messageLu->setDate(new \DateTime());
                $messageLu->setMessage($message);
                $message->addMessageLus($messageLu);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($messageLu);
            $em->persist($message);
        }
        $em->flush();
        
        return $this->json(array('statut' => 1));
    }
}
