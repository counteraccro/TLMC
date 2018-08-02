<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AppController extends Controller
{

    /**
     * Nombre maximum d'éléments pour la pagination
     *
     * @var integer
     */
    const MAX_NB_RESULT = 20;

    /**
     * Indentifiant pour la recherche courante
     *
     * @var string
     */
    const CURRENT_SEARCH = 'current_search';

    /**
     * Tableau de parenté
     *
     * @var array
     */
    const FAMILLE_PARENTE = array(
        1 => 'Père',
        2 => 'Mère',
        3 => 'Frère',
        4 => 'Soeur',
        5 => 'Cousin',
        6 => 'Cousine',
        7 => 'Demi-frère',
        8 => 'Demi-soeur',
        9 => 'Conjoint',
        10 => 'Enfant',
        11 => 'Parrain',
        12 => 'Marraine'
    );

    const QUESTION_TYPE = array(
        'ChoiceType' => 'Liste déroulante',
        'TextType' => 'Champ texte',
        'TextareaType' => 'Zone de texte',
        'CheckboxType' => 'Case à cocher',
        'FileType' => 'Fichier joint',
        'RadioType' => 'Choix unique'
    );
    
    const QUESTION_REGLES_REGEX = array(
        'Chiffres uniquement' => 'd',
        'Caractères spéciaux uniquement' => 'W',
        'Lettres uniquement' => '[a-zA-Z]',
        'Lettres minuscules uniquement' => '[a-z]',
        'Lettres majuscules uniquement' => '[A-Z]',
        'Lettres (min. & maj.) et chiffres' => '[[:alnum:]]',
        'Tout accepter' => '.'
    );
    
    const DROITS = array(
        'ROLE_ADMIN' => 'Administratreur',
        'ROLE_BENEFICIAIRE_DIRECT' => 'Bénéficiaire Direct',
        'ROLE_BENEFICIAIRE' => 'Bénéficiaire',
        'ROLE_BENEVOLE' => 'Bénévole' 
    );

    /**
     * Supprime une recherche de la session
     *
     * @Route("/utils/delete-search/{page}/{key}", name="utils_delete-search", defaults={"key"= null})
     */
    public function deleteSearch(Request $request, SessionInterface $session, int $page, $key = null)
    {
        $paramsSearch = $session->get(self::CURRENT_SEARCH);
        if (isset($paramsSearch[$key])) {
            unset($paramsSearch[$key]);
        }

        $this->pre($paramsSearch);

        $session->set(self::CURRENT_SEARCH, $paramsSearch);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Debug array
     *
     * @param mixed $data
     */
    public function pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * Url de la home du projet
     *
     * @return string
     */
    public function indexUrlProject()
    {
        return $this->generateUrl('index');
    }

    /**
     * Ajoute dans la session le champ field et order pour les tris
     *
     * @param SessionInterface $session
     * @param string $field
     * @param string $order
     */
    public function setDatasFilter(SessionInterface $session, $field, $order)
    {
        $session->set('current_field', $field);
        $session->set('current_order', $order);
    }

    /**
     * Renvoi un tableau contenant le champ field et order des tris
     *
     * @param SessionInterface $session
     * @return array [order] => l'ordre de tri
     *         [field] => champs à trier
     */
    public function getDatasFilter(SessionInterface $session)
    {
        $return = array();
        $return['order'] = $session->get('current_order');
        $return['field'] = $session->get('current_field');

        return $return;
    }

    /**
     * Fonction permettant la recherche à multiples filtres
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param array $params
     *            [order] => l'ordre de tri
     *            [field] => champs à trier
     *            [repositoryClass] => repository (classe) concerné
     *            [repositoryMethode] => méthode à utiliser dans le repository
     *            [page] => indication pour la pagination
     * @return array @return liste des résultats de la recherche
     */
    public function genericSearch(Request $request, SessionInterface $session, array $params)
    {
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);

        $paramsSearch = $session->get(self::CURRENT_SEARCH);

        if (empty($paramsSearch)) {
            $paramsSearch = $request->request->all();
        } else {
            $paramsSearch = array_merge($paramsSearch, $request->request->all());
        }

        foreach ($paramsSearch as $key => $val) {
            $tab = explode('-', $key);
            if ($tab[0] != $params['repository']) {
                unset($paramsSearch[$key]);
            }
        }

        // Vérification si le champ de trie appartient bien à l'object
        $obj = new $params['repositoryClass']();
        $array_methode = get_class_methods($obj);
        $field = str_replace('_', '', $params['field']);
        $is_true = false;
        foreach ($array_methode as $methode) {
            $methode = str_replace('set', '', $methode);
            $methode = strtolower($methode);
            if ($methode == $field) {
                $is_true = true;
                break;
            }
        }

        if (! $is_true) {
            $params['field'] = 'id';
        }

        $paramsRepo = array(
            'field' => $params['field'],
            'order' => $params['order'],
            'repository' => $params['repository'],
            'search' => $paramsSearch,
            'sans_inactif' => (isset($params['sans_inactif']) ? $params['sans_inactif'] : true)
        );

        $session->set(self::CURRENT_SEARCH, $paramsSearch);

        return $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT, $paramsRepo);
    }
    
    /**
     * Renvoie si l'utilisateur connecté a un role admin
     * @return boolean
     */
    public function isAdmin(){
        foreach ($this->getUser()->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Récupération des éléments liés et actifs pour un objet
     *
     * @param $objet
     * @param string $methode
     * @return array
     */
    public function getElementsLiesActifs($objet, $methode)
    {
        $admin = $this->isAdmin();

        if (method_exists($objet, $methode)) {

            if (! $admin) {
                $elements = array();
                foreach ($objet->{$methode}() as $element) {
                    if ($element->getDisabled() == 0) {
                        $elements[] = $element;
                    }
                }
            } else {
                $elements = $objet->{$methode}();
            }
            
        } else {
            $elements = array();
        }
        return $elements;
    }
}