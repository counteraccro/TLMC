<?php
namespace App\Controller;

use App\Entity\Temoignage;
use App\Entity\Questionnaire;
use App\Entity\Evenement;
use App\Entity\Patient;
use App\Entity\Produit;
use App\Service\EmailManager;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AppController
{

    /**
     *
     * @Route("/email", name="email")
     * @param EmailManager $sendEmail
     */
    public function testMailer(EmailManager $sendEmail)
    {
        $params = array(
            'expediteur' => array('toto@gmail.com'),
            'destinataire' => array('ingridweil@gmail.com', 'ingridweil2@gmail.com'),
            'body' => $this->render('emails/registration.html.twig', [
                'nom' => htmlentities($this->getMembre()->getNom()),
                'prenom' => htmlentities($this->getMembre()->getPrenom()),
                'username' => htmlentities($this->getMembre()->getUsername()),
            ]),
        );
        $sendEmail->send($params);
        return $this->render('index/index_visiteur.html.twig');
    }

    /**
     * Page d'accueil + 5 derniers témoignages
     *
     * @Route("/", name="index")
     */
    public function index()
    {
        // s'il s'agit d'un visiteur non-authentifié
        if (! $this->getUser()) {

            return $this->redirectToRoute('security_login');
        }

        // 5 questionnaires dont la date de fin est la plus proche
        $repositoryQuest = $this->getDoctrine()->getRepository(Questionnaire::class);
        $questionnaires = $repositoryQuest->findExpiringSoon();

        // 5 évènements dont la date de début est la plus proche
        $repositoryQuest = $this->getDoctrine()->getRepository(Evenement::class);
        $evenements = $repositoryQuest->getComingSoon();

        // 5 derniers patients enregistrés en base
        $repositoryQuest = $this->getDoctrine()->getRepository(Patient::class);
        $patients = $repositoryQuest->getRecents();

        // 5 derniers produits enregistrés en base
        $repositoryQuest = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $repositoryQuest->getRecents();

        // 5 derniers témoignages créés
        $repositoryTmg = $this->getDoctrine()->getRepository(Temoignage::class);
        $membre = $this->getMembre();
        $specialite = $membre->getSpecialite();
        if (is_null($specialite)) {
            $specialite_id = null;
        } else {
            $specialite_id = $specialite->getId();
        }
        $temoignages_prod = $repositoryTmg->getRecentsTemProd($this->isAdmin(), $specialite_id);
        $temoignages_event = $repositoryTmg->getRecentsTemEvent($this->isAdmin(), $specialite_id);

        /**
         * Début affichage des vues en fonction des droits de l'utilisateur courant
         */

        $roles = $membre->getRoles();
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
                    'temoignages_prod' => $temoignages_prod,
                    'temoignages_event' => $temoignages_event
                ]);
                break;

            // si l'utilisateur courant est bénévole
            case 'ROLE_BENEVOLE':
                return $this->render('index/index_benevole.html.twig', [
                    'temoignages_prod' => $temoignages_prod,
                    'temoignages_event' => $temoignages_event
                ]);
                break;

            // si l'utilisateur courant est bénéficiaire
            case 'ROLE_BENEFICIAIRE':
                return $this->render('index/index_beneficiaire.html.twig', [
                    'temoignages_prod' => $temoignages_prod,
                    'temoignages_event' => $temoignages_event
                ]);
                break;

            // si l'utilisateur courant est bénéficiaire direct
            case 'ROLE_BENEFICIAIRE_DIRECT':
                return $this->render('index/index_beneficiaire_direct.html.twig', [
                    'temoignages_prod' => $temoignages_prod,
                    'temoignages_event' => $temoignages_event
                ]);
                break;
        }
    /**
     * Fin affichage des vues en fonction des droits de l'utilisateur courant
     */
    }
}