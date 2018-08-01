<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Controller\AppController;

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
        return array();
    }

    public function getDroit($key)
    {
        return (isset(AppController::DROITS[$key]) ? AppController::DROITS[$key] : $key);
    }
}
