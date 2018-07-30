<?php
/**
 * Génération automatique des liens pour les vue admins See, Edit, Disabled
 */
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig_Extension;
use Twig\TwigFunction;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActionExtension extends AbstractExtension
{
    
    // @todo a conserver
    /*protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }*/
    
    /**
     * Déclaration des fonctions pour twig
     * {@inheritDoc}
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            /*new TwigFilter('actionsAll', array(
                $this,
                'actionsAllFilter'
            )),*/
        );
    }
    
    public function getFunctions()
    {
        return array(
            new TwigFunction('actionSee', array(
                $this,
                'actionSeeFilter'
            )),
            new TwigFunction('actionEdit', array(
                $this,
                'actionEditFilter'
            )),
            new TwigFunction('actionDelete', array(
                $this,
                'actionDeleteFilter'
            )),
            new TwigFunction('actionsAll', array(
             $this,
             'actionsAllFilter'
             )),
        );
    }

    /**
     * Génération des liens See, Edit et disabled
     * @param int $disabled
     * @param string $url_see
     * @param string $url_edit
     * @param string $url_delete
     * @return string
     */
    public function actionsAllFilter($disabled, $url_see, $url_edit, $url_delete)
    {
        $actionSee = $this->actionSeeFilter($url_see);
        $actionEdit = $this->actionEditFilter($url_edit);
        $actionDelete = $this->actionDeleteFilter($url_delete, $disabled);

        return $actionSee . ' ' . $actionEdit . ' ' . $actionDelete;
    }

    /**
     * Génération du lien See
     * @param string $url_see
     * @return string
     */
    public function actionSeeFilter($url_see)
    {
        return '<a href="' . $url_see . '" title="Voir la fiche"><span class="oi oi-eye"></span></a>';
    }

    /**
     * Génération du lien Edit
     * @param string $url_edit
     * @return string
     */
    public function actionEditFilter($url_edit)
    {
        return '<a href="' . $url_edit . '" title="Editer la fiche"><span class="oi oi-pencil"></span></a>';
    }

    /**
     * Génération du lien delete
     * @param string $url_delete
     * @param string $disabled
     * @return string
     */
    public function actionDeleteFilter($disabled, $url_delete)
    {
        $title = "Désactiver la donnée";
        $icon = "oi-x";
        if ($disabled == 1) {
            $title = "Activer la donnée";
            $icon = "oi-check";
        }

        return '<a href="' . $url_delete . '" title="' . $title . '" class="link-delete"><span class="oi ' . $icon . '"></span></a>';
    }
}