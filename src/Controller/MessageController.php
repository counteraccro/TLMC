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
     *
     * @Route("/messagerie", name="message")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function index()
    {
        return $this->render('message/index.html.twig', []);
    }

    /**
     * Récupère le nombre de message non lu
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
     * @Route("/messagerie/ajax/messagesdestinataire", name="message_ajax_messages_destinataire")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesDestinataire()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);

        $messages = $repository->findByUserByParameter($this->getUser()
            ->getId());

        return $this->render('message/ajax_messages_destinataire.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * Affiche les messages en brouillon du user courant
     *
     * @Route("/messagerie/ajax/messagesbrouillons", name="message_ajax_messages_brouillons")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesBrouillons()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);

        $messages = $repository->findByUserByParameter($this->getUser()
            ->getId(), 1, 'expediteur');

        return $this->render('message/ajax_messages_brouillons.html.twig', [
            'messages' => $messages
        ]);
    }

    /**
     * Affiche les messages placés en corbeille du user courant
     *
     * @Route("/messagerie/ajax/messagescorbeille", name="message_ajax_messages_corbeille")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesCorbeille()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);

        $messages = $repository->findCorbeilleByUser($this->getUser()
            ->getId());

        return $this->render('message/ajax_messages_corbeille.html.twig', [
            'messages' => $messages
        ]);
    }
    
    /**
     * Affiche les messages envoyés par le user courant
     *
     * @Route("/messagerie/ajax/messagesenvoyes", name="message_ajax_messages_envoyes")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesEnvoyes()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);
        
        $messages = $repository->findByUserByParameter($this->getUser()
            ->getId(), 0, 'expediteur');
        
        return $this->render('message/ajax_messages_envoyes.html.twig', [
            'messages' => $messages
        ]);
    }
    
    /**
     * Affiche une prévisualisation du message selectionné par l'user courant
     *
     * @Route("/messagerie/ajax/messagespreview", name="message_ajax_messages_preview")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEVOLE') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     */
    public function ajaxMessagesPreview()
    {
        $repository = $this->getDoctrine()->getRepository(Message::class);
        
        $messages = $repository->findByUserByParameter($this->getUser()
            ->getId(), 0, 'expediteur');
        
        return $this->render('message/ajax_messages_preview.html.twig', [
            'messages' => $messages
        ]);
    }
}
