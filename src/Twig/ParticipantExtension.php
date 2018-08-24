<?php
/**
 * Définition des filtres et fonctions en rapport l'entity Participant
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\ParticipantController;

class ParticipantExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('statutParticipant', array($this, 'getStatutParticipant')),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', array($this, 'doSomething')),
        ];
    }

    /**
     * Renvoie le statut correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutParticipant(int $key)
    {
        return (isset(ParticipantController::STATUT[$key]) ? ParticipantController::STATUT[$key] : $key);
    }
}
