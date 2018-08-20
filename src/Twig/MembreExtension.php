<?php
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

    public function getDroit($key)
    {
        return (isset(AppController::DROITS[$key]) ? AppController::DROITS[$key] : $key);
    }

    /**
     * Bouton desactiver/activer d'un membre
     *
     * @param Membre $membre
     * @param string $url
     * @return string
     */
    public function disabledBtn(Membre $membre, $url)
    {
        if ($membre->getDisabled()) {
            return '<a href="' . $url . '" id="btn-disabled-membre" class="btn btn-secondary"><span class="oi oi-check"></span> Activer</a>';
        } else {
            return '<a href="' . $url . '" id="btn-disabled-membre" class="btn btn-secondary"><span class="oi oi-x"></span> Désactiver</a>';
        }
    }
}
