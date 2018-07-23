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
     * @var string
     */
    const CURRENT_SEARCH = 'current_search';

    /**
     * Supprime une recherche de la session
     *
     * @Route("/utils/delete-search/{page}/{key}", name="utils_delete-search", defaults={"key"= null})
     */
    public function deleteSearch(Request $request, SessionInterface $session, int $page, $key = null)
    {
        $paramsSearch = $session->get(self::CURRENT_SEARCH);
        if(isset($paramsSearch[$key]))
        {
            unset($paramsSearch[$key]);
        }
        $session->set(self::CURRENT_SEARCH, $paramsSearch);
       
        
        return $this->redirect($request->headers->get('referer'));
        
        /*return $this->redirect($this->generateUrl('patient', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        )));*/
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
     * @param Request $request
     * @param SessionInterface $session
     * @param array $params [order] => l'ordre de tri
     *         [field] => champs à trier
     *         [repositoryClass] => repository (classe) concerné
     *         [repositoryMethode] => méthode à utiliser dans le repository
     *         [page] => indication pour la pagination
     * @return array @return liste des résultats de la recherche
     */
    public function genericSearch(Request $request, SessionInterface $session, array $params)
    {
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);
        
        $paramsSearch = $session->get(self::CURRENT_SEARCH);
        
        if(empty($paramsSearch))
        {
            $paramsSearch = $request->request->all();
        }
        else
        {
            $paramsSearch = array_merge($paramsSearch, $request->request->all());
        }
       
        $paramsRepo = array(
            'field' => $params['field'],
            'order' => $params['order'],
            'repository' => $params['repository'],
            'search' => $paramsSearch,
        );
        
        $session->set(self::CURRENT_SEARCH, $paramsSearch);
        
        return $repository->{$params['repositoryMethode']}($params['page'], self::MAX_NB_RESULT, $paramsRepo);
    }
}