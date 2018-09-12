<?php
/**
 * Extension sur tout ce qui touche au message niveau affichage
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Questionnaire;

class DateExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('formatDateMessage', array(
                $this,
                'formatDateMessage'
            )),
            new TwigFunction('formatDateQuestionnaire', array(
                $this,
                'formatDateQuestionnaire'
            ))
        );
    }

    /**
     * Formate une date (affichage différent : Aujourd'hui, hier, il y a une heure...)
     * @param int $date au format timestamp
     * @return string
     */
    function formatDateMessage($date)
    {
        if($date instanceof \DateTime)
        {
            $date = $date->getTimestamp();
        }
        
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
    
    /**
     * Formate une date (compte/décompte de jours, exemple : J-21)
     */
    function formatDateQuestionnaire($date)
    {
        $now = new \DateTime();
        $nbJours = date_diff($now, $date);
        echo $nbJours->format('%R%a jours');
    }
}