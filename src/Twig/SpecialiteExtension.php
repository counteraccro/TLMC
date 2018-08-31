<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Specialite;

class SpecialiteExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('filter_name', array(
                $this,
                'doSomething'
            ))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('infoSpecialite', array(
                $this,
                'getInfoSpecialitePopUp'
            ))
        );
    }

    public function getInfoSpecialitePopUp(Specialite $specialite)
    {
        $return = '';
        
        $return .= '<b>Code logistique:</b> ' . $specialite->getCodeLogistique() . '<br>';
        $return .= '<b>Service pour adulte:</b> ' . ($specialite->getAdulte() ? 'Oui' : 'Non') . '<br>';
        $return .= '<b>Service pour enfant:</b> ' . ($specialite->getPediatrie() ? 'Oui' : 'Non') . '<br>';
        
        return $return;
    }
}
