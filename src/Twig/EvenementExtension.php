<?php
/**
 * Création de filtre pour les données d'un événement
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\EvenementController;

class EvenementExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('type', array($this, 'getType')),
            new TwigFilter('statut', array($this, 'getStatut')),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    /**
     * Renvoie le type correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getType(int $key)
    {
        return (isset(EvenementController::TYPE[$key]) ? EvenementController::TYPE[$key] : $key);
    }
    
    /**
     * Renvoie le staut correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatut(int $key)
    {
        return (isset(EvenementController::STATUT[$key]) ? EvenementController::STATUT[$key] : $key);
    }
}
