<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Temoignage;
use App\Entity\Questionnaire;

class IndexController extends AppController
{

    /**
     * Page d'accueil + 5 derniers témoignages
     *
     * @Route("/", name="index")
     */
    public function index()
    {
        $repositoryQuest = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $repositoryQuest->findExpiringSoon();
        
//         foreach ($questionnaires as $quest) {
//             $now = new \DateTime();
//             $dateFin = $quest->getDateFin();
//             $nbJours = $now->diff($dateFin);
//             $nbJours->format('%R%a days');
//         }

        $repositoryTmg = $this->getDoctrine()->getRepository(Temoignage::class);

        $params = array(
            'order' => 'DESC',
            'field' => 'date_creation',
            'repository' => 'Temoignage',
            'condition' => array(
                'Temoignage.disabled = 0'
            )
        );

        $temoignages = $repositoryTmg->findAllTemoignages(1, 5, $params);
        
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'questionnaires' => $questionnaires,
            //'nbJours' => $nbJours,
            'temoignages' => $temoignages['paginator']
        ]);
    }
}