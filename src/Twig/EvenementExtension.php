<?php
/**
 * Création de filtre pour les données d'un événement
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\EvenementController;
use App\Controller\SpecialiteEvenementController;

class EvenementExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('typeEvent', array($this, 'getTypeEvenement')),
            new TwigFilter('statutEvent', array($this, 'getStatutEvenement')),
            new TwigFilter('statutSpeEvent', array($this, 'getStatutSpecialiteEvenement')),
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('horaire', array($this, 'getHoraireEvent')),
        );
    }

    /**
     * Renvoie le type correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getTypeEvenement(int $key)
    {
        return (isset(EvenementController::TYPE[$key]) ? EvenementController::TYPE[$key] : $key);
    }
    
    /**
     * Renvoie le statut correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutEvenement(int $key)
    {
        return (isset(EvenementController::STATUT[$key]) ? EvenementController::STATUT[$key] : $key);
    }
    
    /**
     * Renvoie le statut du lien evenement - spécialité correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutSpecialiteEvenement(int $key)
    {
        return (isset(SpecialiteEvenementController::STATUT[$key]) ? SpecialiteEvenementController::STATUT[$key] : $key);
    }
    
    /**
     * Affichage des horaires d'un événement
     *
     * @param \DateTime $date_naissance
     * @return number
     */
    public function getHoraireEvent(\DateTime $date_debut, \DateTime $date_fin)
    {
        if($date_debut->format('z') == $date_fin->format('z')){
            return $date_debut->format('d/m/Y'). ' de ' . $date_debut->format('H:i') . ' à ' . $date_fin->format('H:i');
        }
        return $date_debut->format('d/m/Y H:i') . ' - ' . $date_fin->format('d/m/Y H:i');
    }
}
