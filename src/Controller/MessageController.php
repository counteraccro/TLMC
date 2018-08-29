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
            'messages' => $result['paginator']
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
            'messages' => $result['paginator']
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
     * @Route("/messagerie/ajax/viewmessage/{id}", name="message_ajax_view_message")
     * @ParamConverter("message", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxViewMessage(Message $message)
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
            'message' => $message
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
}
