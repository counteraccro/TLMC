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
    public function listingMessage(Message $message, $membre_id, $path, $statut = 'destinataire')
    {
        $html = '';

        // Détection si le message est lu ou non
        $mslu = '<span class="oi oi-envelope-closed"></span>';
        $isRead = 'no-read';
        /* @var \App\Entity\MessageLu $messageLu */
        foreach ($message->getMessageLus() as $messageLu) {
            if ($messageLu->getMembre()->getId() == $membre_id) {
                if ($messageLu->getLu() == 1) {
                    $mslu = '<span class="oi oi-envelope-open"></span>';
                    $isRead = '';
                }
            }
        }
        
        $html .= '<a href="' . $path . '" class="list-group-item list-group-item-action ' . $isRead . '">';

        $nomPrenom = '';
        if ($statut == 'destinataire') {
            $nomPrenom = '<div class="small-head">' . $message->getExpediteur()->getNom() . ' ' . $message->getExpediteur()->getPrenom() . '</div>';
        }

        $html .= $mslu . ' ' . $nomPrenom . '<br /><div class="small-body">' . $message->getTitre() . '</div>';
        $html .= '<div class="float-right">' . $this->formatDate($message->getDateEnvoi()->getTimestamp()) . '</div>';
        
        $html .= '</a>';

        return $html;
    }

    /**
     * Format une date
     * @param int $date au format timestamp
     * @return string
     */
    function formatDate($date)
    {
        if (! ctype_digit($date)) {
            $date = strtotime($date);
        }
        if (date('Ymd', $date) == date('Ymd')) {
            $diff = time() - $date;
            /* moins de 60 secondes */
            if ($diff < 60) {
                return 'Il y a ' . $diff . ' sec';
            } /* moins d'une heure */
            else if ($diff < 3600) {
                return 'Il y a ' . round($diff / 60, 0) . ' min';
            } /* moins de 3 heures */
            else if ($diff < 10800) {
                return 'Il y a ' . round($diff / 3600, 0) . ' heures';
            } /* plus de 3 heures ont affiche ajourd'hui à HH:MM:SS */
            else {
                return 'Aujourd\'hui à ' . date('H:i:s', $date);
            }
        } else if (date('Ymd', $date) == date('Ymd', strtotime('- 1 DAY'))) {
            return 'Hier à ' . date('H:i:s', $date);
        }
        else if (date('Ymd', $date) == date('Ymd', strtotime('- 2 DAY'))) {
            return 'Il y a 2 jours à ' . date('H:i:s', $date);
        }
        else
        {
            return 'Le ' . date('d/m/Y à H:i:s', $date);
        }
    }
}