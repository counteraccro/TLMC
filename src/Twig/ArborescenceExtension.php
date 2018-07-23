<?php
/**
 * Génération automatique du fil d'ariane
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Extension;
use Twig\TwigFunction;

class ArborescenceExtension extends AbstractExtension
{

    /**
     * Déclaration des fonctions pour twig
     *
     * {@inheritdoc}
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('arborescence', array(
                $this,
                'getArborescence'
            ))
        );
    }

    /**
     * Génère un fil d'ariane en fonction d'un tableau d'url
     *
     * @param array $paths
     *            tableau comprenant les clées / valeurs
     *            [home] => url home du projet
     *            [urls] => tableau d'urls sous la forme url => label
     *            [active] => page courante
     * @return string
     */
    public function getArborescence(array $paths)
    {
        $arbo = '<nav aria-label="breadcrumb">';
        $arbo .= '<ol class="breadcrumb">';
        $arbo .= '<li class="breadcrumb-item"><a href="' . $paths['home'] . '" title="Home"><span class="oi oi-home"></span></a></li>';
        
        if(isset($paths['urls']))
        {
            foreach ($paths['urls'] as $path => $nom) {
                $arbo .= '<li class="breadcrumb-item"><a href="' . $path . '">' . $nom . '</a></li>';
            }
        }
        $arbo .= '<li class="breadcrumb-item active">' . $paths['active'] . '</li>';
        $arbo .= '</ol>';
        $arbo .= '</nav>';
        return $arbo;
    }
}
    