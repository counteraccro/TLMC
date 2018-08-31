<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Temoignage;

class IndexController extends AppController
{

    /**
     * Page d'accueil + 5 derniers témoignages
     *
     * @Route("/", name="index")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Temoignage::class);

        $params = array(
            'order' => 'DESC',
            'field' => 'date_creation',
            'repository' => 'Temoignage',
            'condition' => array(
                'Temoignage.disabled = 0'
            )
        );

        $temoignages = $repository->findAllTemoignages(1, 5, $params);
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'temoignages' => $temoignages['paginator']
        ]);
    }
}