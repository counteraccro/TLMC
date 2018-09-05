<?php
/**
 * Création de filtres pour les données d'un produit
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Controller\ProduitController;

class ProduitExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('typeProduit', array($this, 'getTypeProduit')),
            new TwigFilter('genre', array($this, 'getGenre')),
        ];
    }
    
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('function_name', array($this, 'doSomething')),
        );
    }
    
    /**
     * Renvoie le type correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getTypeProduit(int $key)
    {
        return (isset(ProduitController::TYPE[$key]) ? ProduitController::TYPE[$key] : $key);
    }
    
    /**
     * Renvoie le genre correspondant à  une clé ou la clé s'il n'y a pas de correspondance
     *
     * @param int $key
     * @return string
     */
    public function getGenre(int $key)
    {
        return (isset(ProduitController::GENRE[$key]) ? ProduitController::GENRE[$key] : $key);
    }
}