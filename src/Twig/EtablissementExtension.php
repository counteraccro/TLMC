<?php
/**
 * Définition des filtres et fonctions en rapport avec l'établissement
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\EtablissementController;

class EtablissementExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('statutConvention', array($this, 'getStatutConvention')),
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('function_name', array($this, 'doSomething')),
        );
    }

    /**
     * Renvoie le statut de la convention correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutConvention(int $key)
    {
        return (isset(EtablissementController::STATUT_CONVENTION[$key]) ? EtablissementController::STATUT_CONVENTION[$key] : $key);
    }
}
