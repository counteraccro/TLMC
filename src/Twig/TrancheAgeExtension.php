<?php
/**
 * Traitement des tranches d'âge dans twig
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\AppController;

class TrancheAgeExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('trancheAge', array(
                $this,
                'getTrancheAge'
            ))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('function_name', array(
                $this,
                'doSomething'
            ))
        );
    }

    /**
     * Renvoie la tranche d'âge correspondant à une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param boolean $value
     * @return string
     */
    public function getTrancheAge($key)
    {
        return (isset(AppController::TRANCHE_AGE[$key]) ? AppController::TRANCHE_AGE[$key] : $key);
    }
}