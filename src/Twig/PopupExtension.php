<?php
/**
 * Affichage d'une popup
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PopupExtension extends AbstractExtension
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
            new TwigFunction('popup', array(
                $this,
                'getPopup'
            ))
        );
    }
    
    /**
     * Affichage d'une popup (l'ajout de javascript est nécessaire)
     * 
     * @param string $texte
     * @param string $titre
     * @return string
     */
    public function getPopup(string $texte, string $titre = null)
    {
        $return = '<a tabindex="0" role="button" data-toggle="popover" data-html="true" data-trigger="hover" ' . (!is_null($titre) ? 'title="' . $titre . '"' : '') .' data-content="' . $texte . '"><span class="oi oi-info"></span></a>';
        return $return;
    }
}
