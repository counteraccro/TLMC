<?php
/**
 * Définition des filtres et fonctions en rapport avec le membre
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\AppController;
use App\Entity\Membre;

class MembreExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('droit', array(
                $this,
                'getDroit'
            ))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('desactiverMembre', array(
                $this,
                'disabledBtn'
            ))
        );
    }

    /**
     * Donne les droits correspondants
     * @param int $key
     * @return int|string
     */
    public function getDroit($key)
    {
        return (isset(AppController::DROITS[$key]) ? AppController::DROITS[$key] : $key);
    }
}
