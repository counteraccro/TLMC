<?php
namespace App\Twig;
/**
 * Traitement des images
 */
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\AppController;

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
     * @param string $classe
     * @return boolean|string
     */
    public function afficherImage(string $element, string $image, string $classe = "img-fluid")
    {
        if(is_null($image)){
            return false;
        }
        $element = strtolower($element);
        if (!in_array($element, AppController::TYPE_AVEC_IMAGE)) {
            return false;
        }
        
        if (preg_match("/^(http|https)/", $image)) {
            return '<img src="' . $image . '" class="' . $classe . '" />';
        }
        return '<img src="/img/' . $element . '/' . $image . '" class="' . $classe . '" />';
    }
}
