<?php
/**
 * Définition des filtres et fonctions en rapport avec le patient
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Extension;
use Twig\TwigFilter;

class PatientExtension extends AbstractExtension
{

    /**
     * Déclaration des filtres twig
     * 
     * {@inheritDoc}
     * @see Twig_Extension::getFilters()
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('age', array(
                $this,
                'getAge'
            ))
        );
    }

    /**
     * Déclaration des fonctions twig
     * 
     * {@inheritDoc}
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions(): array
    {
        return array();
    }

    
    /**
     * Calcul de l'âge d'un patient
     * 
     * @param \DateTime $date_naissance
     * @return number
     */
    public function getAge($date_naissance)
    {
        $date_reference = new \DateTime();

        $diff = $date_reference->diff($date_naissance);

        return $diff->y;
    }
}
