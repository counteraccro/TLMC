<?php
/**
 * Définition des filtres et fonctions en rapport avec l'établissement
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\EtablissementController;
use App\Entity\Etablissement;

class EtablissementExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('statutConvention', array(
                $this,
                'getStatutConvention'
            ))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('infoEtablissement', array(
                $this,
                'getInfoEtablissementPopUp'
            ))
        );
    }

    /**
     * Renvoie le statut de la convention correspondant à une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutConvention(int $key)
    {
        return (isset(EtablissementController::STATUT_CONVENTION[$key]) ? EtablissementController::STATUT_CONVENTION[$key] : $key);
    }

    /**
     * Renvoie les informations d'un établissement sous la forme d'une chaine de caractères
     *
     * @param Etablissement $etablissement
     * @return string
     */
    public function getInfoEtablissementPopUp(Etablissement $etablissement)
    {
        $return = '';

        $return .= '<b>Code logistique:</b> ' . $etablissement->getCodeLogistique() . '<br>';
        $return .= '<b>Nombre de lits:</b> ' . $etablissement->getNbLit() . '<br>';
        $return .= '<b>Statut de la convention:</b> ' . $this->getStatutConvention($etablissement->getStatutConvention()) . '<br>';
        $return .= '<b>Date de début de collaboration:</b> ' . $etablissement->getDateCollaboration()->format('d/m/Y') . '<br>';
        $return .= '<b>Adresse:</b><br> ' . $etablissement->getNumeroVoie() . ', ' . $etablissement->getVoie() . '<br>' . $etablissement->getCodePostal() . ' ' . $etablissement->getVille() . '<br>';

        return $return;
    }
}
