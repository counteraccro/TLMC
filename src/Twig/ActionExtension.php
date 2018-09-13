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
            new TwigFunction('editObjet', array(
                $this,
                'actionEditObjet'
            )),
            new TwigFunction('deleteObjet', array(
                $this,
                'actionDeleteObjet'
            )),
            new TwigFunction('deleteImage', array(
                $this,
                'actionDeleteImage'
            )),
            new TwigFunction('seeImages', array(
                $this,
                'actionSeeImages'
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
     * @param string $id
     * @return string
     */
    public function actionEditFilter($url_edit, string $id = null)
    {
        $return = '<a href="' . $url_edit . '"';
        if(!is_null($id)){
            $return .= ' id="' . $id . '"';
        }
        $return .= ' title="Editer la fiche"><span class="oi oi-pencil"></span></a>';
        return $return;
    }

    /**
     * Génération du lien delete
     * @param string $url_delete
     * @param string $disabled
     * @return string
     */
    public function actionDeleteFilter($disabled, $url_delete, $id = null)
    {
        $title = "Désactiver la donnée";
        $icon = "oi-x";
        if ($disabled == 1) {
            $title = "Activer la donnée";
            $icon = "oi-check";
        }

        return '<a href="' . $url_delete . '" title="' . $title . '" class="link-delete" ' . ($id ? 'id="delete-' . $id . '"' : '') . '><span class="oi ' . $icon . '"></span></a>';
    }
    
    /**
     * Génération du bouton edit dans les fiches des éléments
     * 
     * @param string $url
     * @param string $label
     * @param string $id
     * @return string
     */
    public function actionEditObjet(string $url, string $label, string $id = null){
        $return = '<a href="' . $url . '"';
        if(!is_null($id)){
            $return .= ' id="' . $id . '"';
        }
        $return .= ' class="btn btn-dark"><span class="oi oi-pencil"></span> ' . $label . '</a>';
        
        return $return;
    }
    
    /**
     * Bouton desactiver/activer d'un objet
     *
     * @param int $disabled
     * @param string $url_delete
     * @param string $id
     * @return string
     */
    public function actionDeleteObjet($disabled, $url_delete, string $id = null)
    {
        $return = '<a href="' . $url_delete . '"';
        
        if(!is_null($id)){
            $return .= ' id="' . $id . '"';
        }
        
        $label = "Désactiver";
        $icon = "oi-x";
        if ($disabled == 1) {
            $label = "Activer";
            $icon = "oi-check";
        }
        $return .= ' class="btn btn-secondary"><span class="oi ' . $icon . '"></span> '. $label . '</a>';
        
        return $return;
    }
    
    /**
     * Bouton pour supprimer une image
     * 
     * @param string $url_delete
     * @param string $id
     * return string
     */
    public function actionDeleteImage($url_delete, string $id = null){
        return '<a href="' . $url_delete . '" ' . (is_null($id) ? '' :  'id="' . $id . '"') . ' class="float-right align-top" title="Supprimer l\'image"><span class="oi oi-circle-x"></span></a>';
    }
    
    /**
     * Bouton pour voir les images d'un témoignage
     *
     * @param string $url_see
     * @param string $id
     * return string
     */
    public function actionSeeImages($url_see, string $id = null){
        return '<a href="' . $url_see . '" ' . (is_null($id) ? '' :  'id="' . $id . '"') . ' class="align-top" title="Voir les images assoicées"><span class="oi oi-image"></span></a>';
    }
}