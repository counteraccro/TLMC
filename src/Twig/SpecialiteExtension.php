<?php
/**
 * Définition des filtres et fonctions en rapport avec la spécialité
 */
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

    /**
     * Renvoie les informations d'une spécialité sous la forme d'une chaine de caractères
     * 
     * @param Specialite $specialite
     * @return string
     */
    public function getInfoSpecialitePopUp(Specialite $specialite)
    {
        $return = '';
        
        $return .= '<b>Code logistique:</b> ' . $specialite->getCodeLogistique() . '<br>';
        $return .= '<b>Service pour adulte:</b> ' . ($specialite->getAdulte() ? 'Oui' : 'Non') . '<br>';
        $return .= '<b>Service pour enfant:</b> ' . ($specialite->getPediatrie() ? 'Oui' : 'Non') . '<br>';
        
        return $return;
    }
}
