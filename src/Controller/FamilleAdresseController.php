<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FamilleAdresse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FamilleAdresseController extends AppController
{
    /**
     * Listing des adresses des familles
     * @Route("/famille_adresse/{page}/{field}/{order}", name="famille_adresse", defaults={"page" = 1, "field"= null, "order"= null})
     */
    public function index(Request $request, SessionInterface $session, int $page = 1, $field = null, $order = null)
    {
        if (is_null($field)) {
            $field = 'id';
        }
        
        if (is_null($order)) {
            $order = 'DESC';
        }
        
        $session->set(self::CURRENT_SEARCH, array());
        
        $params = array(
            'field' => $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => FamilleAdresse::class,
            'repository' => 'Famille',
            'repositoryMethode' => 'findAllFamilleAdresses'
        );
        $result = $this->genericSearch($request, $session, $params);
        
        $pagination = array(
            'page' => $page,
            'route' => 'famille_adresse',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );
        
        $this->setDatasFilter($session, $field, $order);
        
        return $this->render('famille_adresse/index.html.twig', [
            'controller_name' => 'FamilleAdresseController',
            'adresses' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste d'adresses de famille"
            )
        ]);
    }
}
