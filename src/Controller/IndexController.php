<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Temoignage;
use App\Entity\Questionnaire;
use App\Entity\Evenement;
use App\Entity\Patient;
use App\Entity\Produit;

class IndexController extends AppController
{

    /**
     * Page d'accueil + 5 derniers témoignages
     *
     * @Route("/", name="index")
     */
    public function index()
    {
        // 5 questionnaires dont la date de fin est la plus proche
        $repositoryQuest = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $repositoryQuest->findExpiringSoon();

        // 5 derniers témoignages créés
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

        // 5 évènements dont la date de début est la plus proche
        $repositoryQuest = $this->getDoctrine()->getRepository(Evenement::class);
        $evenements = $repositoryQuest->getComingSoon();
        
        // 5 derniers patients enregistrés en base
        $repositoryQuest = $this->getDoctrine()->getRepository(Patient::class);
        $patients = $repositoryQuest->getRecents();
        
        // 5 derniers produits enregistrés en base
        $repositoryQuest = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $repositoryQuest->getRecents();
        
        /**
         *  Début affichage des vues en fonction des droits de l'utilisateur courant
         */
        //s'il s'agit d'un visiteur non-authentifié
        if (! $this->getUser()) {
            return $this->render('index/index_visiteur.html.twig', [
                'temoignages' => $temoignages['paginator']
            ]);
        }

        $roles = $this->getMembre()->getRoles();
        // Cas car ici on n'a qu'un seul rôle, a adapter si besoin (à l'avenir)
        $role = $roles[0];

        switch ($role) {
            // si l'utilisateur courant est administrateur
            case 'ROLE_ADMIN':
                return $this->render('index/index.html.twig', [
                    'questionnaires' => $questionnaires,
                    'evenements' => $evenements,
                    'patients' => $patients,
                    'produits' => $produits,
                    'temoignages' => $temoignages['paginator']
                ]);
                break;
                
            // si l'utilisateur courant est bénévole
            case 'ROLE_BENEVOLE':
                return $this->render('index/index_benevole.html.twig', [
                'temoignages' => $temoignages['paginator']
                ]);
                break;
                
                // si l'utilisateur courant est bénéficiaire
            case 'ROLE_BENEFICIAIRE':
                return $this->render('index/index_beneficiaire.html.twig', [
                'temoignages' => $temoignages['paginator']
                ]);
                break;
                
                // si l'utilisateur courant est bénéficiaire direct
            case 'ROLE_BENEFICIAIRE_DIRECT':
                return $this->render('index/index_beneficiaire_direct.html.twig', [
                'temoignages' => $temoignages['paginator']
                ]);
                break;
        }
        /**
         *  Fin affichage des vues en fonction des droits de l'utilisateur courant
         */
    }
}