<?php
/**
 * Extension sur tout ce qui touche au message niveau affichage
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Controller\AppController;
use App\Entity\Message;

class MessageExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('listingMessage', array(
                $this,
                'listingMessage'
            ))
        );
    }

    /**
     * Renvoie la tranche d'âge correspondant à une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param boolean $value
     * @return string
     */
    public function listingMessage(Message $message, $membre_id, $statut = 'destinataire')
    {
        $html = '';

        $html .= '<a href="#" class="list-group-item list-group-item-action">';

        $mslu = '<span class="oi oi-envelope-open"></span>';

        /* @var \App\Entity\MessageLu $messageLu */
        foreach ($message->getMessageLus() as $messageLu) {
            if ($messageLu->getMembre()->getId() == $membre_id) {
                if ($messageLu->getLu() == 1) {
                    $mslu = '<span class="oi oi-envelope-closed"></span>';
                }
            }
        }

        $nomPrenom = '';
        if ($statut == 'destinataire') {
            $nomPrenom = '<div class="small-head">' . $message->getExpediteur()->getNom() . ' ' . $message->getExpediteur()->getPrenom() . '</div>';

            $date = '';
            $today = new \DateTime();
            
            if ($message->getDateEnvoi()->format('d-m-Y') == $today->format('d-m-Y')) {
                $date = 'Aujourd\'hui';
            }
//             else if ($message->getDateEnvoi()->format('d-m-Y') != $today->format('d-m-Y')) {
//                 $date = 'Hier';
//             }
            else {
                $date = '';
            }
            $dateReception = '<div class="float-right">' . $date . ' ' . $message->getDateEnvoi()->format('d-m-Y') . '</div>';
        }

        $html .= $mslu . ' ' . $nomPrenom . '<br /><div class="small-body">' . $message->getTitre() . ' / ' . $dateReception . '</div>';

        $html .= '</a>';

        return $html;
    }
}