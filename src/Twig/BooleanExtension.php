<?php
/**
 * Traitement des booléens dans twig
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BooleanExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('bool', array(
                $this,
                'getOuiNon'
            )),
            new TwigFilter('actif', array(
                $this,
                'isActif'
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
     * Renvoie Oui ou Non en fonction de $value
     *
     * @param boolean $value
     * @return string
     */
    public function getOuiNon($value)
    {
        return ($value == 1 ? 'Oui' : 'Non');
    }
    
    /**
     * Renvoie Oui ou Non en fonction de $value
     *
     * @param boolean $value
     * @return string
     */
    public function isActif($value, $avec_pastille = false)
    {
        if($avec_pastille){
            return ($value == 0 ? '<span class="text-success"><span class="oi oi-media-record"></span></span> Oui' : '<span class="text-danger"><span class="oi oi-media-record"></span></span> Non');
        }
        return ($value == 0 ? 'Oui' : 'Non');
    }
}
