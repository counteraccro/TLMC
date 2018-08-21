<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
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
            new TwigFunction('image', array(
                $this,
                'afficherImage'
            ))
        );
    }

    /**
     * Affichage d'une image pour un événement ou un produit
     *
     * @param string $element
     * @param string $image
     * @return boolean|string
     */
    public function afficherImage(string $element, string $image)
    {
        $element = strtolower($element);
        if ($element != 'produit' && $element != 'evenement') {
            return false;
        }
        if (preg_match("/^(http|https)/", $image)) {
            return '<img src="' . $image . '" width="75%"/>';
        }
        return '<img src="/img/' . $element . '/' . $image . '" width="75%"/>';
    }
}
