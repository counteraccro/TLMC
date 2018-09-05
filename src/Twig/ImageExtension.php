<?php
namespace App\Twig;
/**
 * Traitement des images
 */
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
     * Affichage d'une image pour un événement, un produit ou un membre
     *
     * @param string $element
     * @param string $image
     * @return boolean|string
     */
    public function afficherImage(string $element, string $image, string $classe = null)
    {
        if(is_null($image)){
            return false;
        }
        $element = strtolower($element);
        if ($element != 'produit' && $element != 'evenement' && $element != 'membre') {
            return false;
        }
        
        if(is_null($classe)){
            $classe = "img-fluid";
        }
        
        if (preg_match("/^(http|https)/", $image)) {
            return '<img src="' . $image . '" class="' . $classe . '" />';
        }
        return '<img src="/img/' . $element . '/' . $image . '" class="' . $classe . '" />';
    }
}
