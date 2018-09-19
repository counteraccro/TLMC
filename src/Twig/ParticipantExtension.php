<?php
/**
 * Définition des filtres et fonctions en rapport l'entity Participant
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\ParticipantController;

class ParticipantExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return array(
            new TwigFilter('statutParticipant', array(
                $this,
                'getStatutParticipant'
            ))
        );
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('selectStatut', array(
                $this,
                'getSelectStatut'
            ))
        ];
    }

    /**
     * Renvoie le statut correspondant à une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getStatutParticipant(int $key)
    {
        return (isset(ParticipantController::STATUT[$key]) ? ParticipantController::STATUT[$key] : $key);
    }

    /**
     * Renvoie une liste déroulante des statuts pour un participant
     * 
     * @param string $name
     * @param int $value
     * @param string $id
     * @return string
     */
    public function getSelectStatut(string $name, int $value = 0, string $id = null)
    {
        $return = '<select class="form-control" name="' . $name . '" ' . ($id ? 'id="' . $id . '"' : '') . '>';
        foreach (ParticipantController::STATUT as $key => $val) {
            $return .= '<option value="' . $key . '" ' . ($key == $value ? 'selected' : '') . '>' . $val . '</option>';
        }
        $return .= '</select>';

        return $return;
    }
}
