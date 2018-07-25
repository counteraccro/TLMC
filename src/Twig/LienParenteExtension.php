<?php
/**
 * Donne le lien de parenté d'une famille
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Extension;
use App\Controller\AppController;
use Twig\TwigFilter;

class LienParenteExtension extends AbstractExtension
{
    /**
     * Déclaration des fonctions pour twig
     *
     * {@inheritdoc}
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('lienParente', array(
                $this,
                'getLienParente'
            ))
        );
    }
    
    /**
     * {@inheritDoc}
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
        );
    }
    
    /**
     * Renvoie le lien de parenté correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     * 
     * @param int $key
     * @return string
     */
    public function getLienParente(int $key)
    {
        return (isset(AppController::FAMILLE_PARENTE[$key]) ? AppController::FAMILLE_PARENTE[$key] : $key);
    }
}
