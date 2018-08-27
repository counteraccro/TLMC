<?php
/**
 * Génération automatique des filtres et champs de recherches
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Extension;
use Twig\TwigFunction;

class FilterExtension extends AbstractExtension
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
     *
     * {@inheritdoc}
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('headerFilter', array(
                $this,
                'headerFilter'
            )),
            new TwigFunction('inputFilter', array(
                $this,
                'inputFilter'
            )),
            new TwigFunction('labelFilter', array(
                $this,
                'labelFilter'
            ))
        );
    }

    /**
     * Permet de déterminer si c'est un filtre croissant ou décroissant
     *
     * @param string $url
     * @param string $label
     * @param string $current_order
     * @param string $current_field
     * @return string
     */
    public function headerFilter($url, $label, $current_order, $current_field)
    {
        if ($current_order == 'ASC') {
            $url = $url . "/DESC";
            $icon = '<span class="oi oi-arrow-top"></span>';
        } else {
            $url = $url . "/ASC";
            $icon = '<span class="oi oi-arrow-bottom"></span>';
        }

        if (! preg_match('/\b' . $current_field . '/', $url)) {
            $icon = '';
        }

        $return = '<a href="' . $url . '">' . $label . ' ' . $icon . '</a>';

        return $return;
    }

    /**
     * Génère les minis-formulaires de recherche
     *
     * @param string $url
     * @param string $field
     * @param array $array_values
     * @param string $placeholder
     * @param array $dropdown_values
     * @return string
     */
    public function inputFilter($url, $field, $array_values = array(), $placeholder = null, array $dropdown_values = array())
    {
        if (is_null($placeholder)) {
            $placeholder = "Recherche...";
        }

        $value = "";
        foreach ($array_values as $key => $val) {
            if ($key == $field) {
                $value = $val;
            }
        }

        $id = $field . '-' . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);

        $return = '<form action="' . $url . '" method="post">
                    <div class="input-group mb-0">';

        if (empty($dropdown_values)) {
            $return .= '<input type="text" class="form-control" placeholder="' . $placeholder . '" id="' . $id . '" name="' . $field . '" value="' . $value . '">';
        } else {
            $return .= '<select name="' . $field . '" class="form-control">';
            $return .= '<option value=""></option>';
            foreach ($dropdown_values as $valeur => $nom) {
                $return .= '<option value="' . $valeur . '" ' . ($valeur == $value ? 'selected' : '') . '>' . $nom . '</option>';
            }
            $return .= '</select>';
        }
        $return .= '<div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit" id="button-addon2"><span class="oi oi-magnifying-glass"></span></button>
              </div>
            </div>
        </form>';

        return $return;
    }

    /**
     * Génère les étiquettes correspondant aux champs de recherche utilisés
     *
     * @param string $url
     * @param array $array_values
     * @return string
     */
    public function labelFilter($url, $array_values = array())
    {
        $return = "";
        foreach ($array_values as $key => $val) {
            if (! empty($val)) {
                $filtres = explode('-', str_replace('_', ' ', $key));

                $return .= '<a href="' . $url . '/' . $key . '" class="badge badge-primary">' . ucfirst($filtres['1']) . (isset($filtres['2']) ? ' ' . ucfirst($filtres['2']) : '') . ' <b>X</b></a> ';
            }
        }

        if (! empty($return)) {
            $return = " - Critère(s) de recherche : " . $return;
        }

        return $return;
    }
}