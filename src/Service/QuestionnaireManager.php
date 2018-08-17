<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry as Doctrine;
use App\Entity\Questionnaire;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Controller\AppController;
use App\Entity\Membre;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class QuestionnaireManager extends AppService
{

    /**
     * Object request
     *
     * @var Request
     */
    private $request;

    /**
     * Object doctrine
     *
     * @var Doctrine
     */
    private $doctrine;

    /**
     * Object questionnaire
     *
     * @var Questionnaire
     */
    private $questionnaire;
    
    /**
     * Si au moins une erreur true
     * @var boolean
     */
    private $is_error = false;

    /**
     * Tableau de retour contenant une liste d'objet contenant
     * stdClass->reponse => object reponse
     * stdClass->question => object question
     * stdClass->erreur => object stdClass contenant
     * * *stdClass->isValide => true|false
     * * *stdClass->label => texte de l'erreur
     * stdClass->valideSubmit => true|false information submit // pour la redirection après enregistrement en statut prod
     */
    private $return = array();

    /**
     *
     * @param Doctrine $doctrine
     * @param RequestStack $requestStack
     */
    public function __construct(Doctrine $doctrine, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->doctrine = $doctrine;
    }

 
    /**
     * Fonction qui appelle les fonctions questionnaire (initialisation, création réponse, validation...)
     * Le membre est obligatoire pour l'enregistrement en base
     * @param Questionnaire $questionnaire
     * @param string $statut
     * @param Membre $membre
     * @return array
     */
    public function manage(Questionnaire $questionnaire, $statut = AppController::PROD, Membre $membre = null)
    {
        $this->questionnaire = $questionnaire;
        $this->initialize();
        $this->createReponse();
        $this->validate();

        // En statut PROD et si pas d'erreur on enregistre en base
        if(!$this->is_error && $statut == AppController::PROD)
        {
            if(is_null($membre))
            {
                throw new \ErrorException('Le membre est obligatoire pour enregistrer les réponses en base de données');
            }
            
           $this->saveData($membre);
        }
        
        return array('result' => $this->return, 'validateSubmit' => !$this->is_error);
    }

    /**
     * Fonction permettant d'initialiser pour le Questionnaire les questions et les réponses
     * On fait entrer dans notre tableau d'objets les données
     */
    private function initialize()
    {
        foreach ($this->questionnaire->getQuestions() as $question) {
            $array = [
                'question' => $question,
                'reponse' => new Reponse(),
                'erreur' => (object) array(
                    'is' => false,
                    'libelle' => '',
                    'regle' => ''
                ),
            ];
            $this->return[$question->getid()] = (object) $array;
        }
    }

    /**
     * Contrôle la validité des réponses saisies en fonctions des règles prévues par le créateur de Questionnaire
     * Renvoie des messages d'erreurs si besoin (caractère obligatoire de la question par exemple)
     */
    private function validate()
    {
        foreach ($this->return as $key => $object) {
            /* @var Question $question */
            $question = $object->question;

            /* @var Reponse $reponse */
            $reponse = $object->reponse;

            if ($question->getObligatoire()) {
                if ($reponse->getValeur() == "") {

                    $this->return[$key]->erreur->is = true;
                    $this->return[$key]->erreur->libelle = 'Cette question est obligatoire';
                    $this->return[$key]->erreur->regle = 'Question obligatoire';
                    $this->is_error = true;
                    continue;
                }
            }

            // Si le Type de question est un champ ou une zone de texte
            if (in_array($question->getType(), array(
                AppController::TEXTAREATYPE,
                AppController::TEXTYPE
            ))) {
                if (! preg_match('/' . $question->getRegles() . '/', $reponse->getValeur())) {
                    $this->return[$key]->erreur->is = true;
                    $this->return[$key]->erreur->libelle = $question->getMessageErreur();
                    $this->return[$key]->erreur->regle = 'Regle de validation : ' . AppController::QUESTION_REGLES_REGEX[$question->getRegles()];
                    $this->is_error = true;
                    continue;
                }
            }

            // Si le type de question correspond à une liste déroulante
            if (in_array($question->getType(), array(
                AppController::CHOICETYPE
            ))) {

                if ($question->getValeurDefaut() == $reponse->getValeur()) {
                    $this->return[$key]->erreur->is = true;
                    $this->return[$key]->erreur->libelle = $question->getMessageErreur();
                    $this->return[$key]->erreur->regle = 'Valeur par défaut = valeur saisie';
                    $this->is_error = true;
                    continue;
                }
            }
        }
    }

    /**
     * Récupération des données renvoyées dans la réponse, pour chaque question renseignée
     * Les élements de réponse sont intégrés à notre objet Réponse
     */
    private function createReponse()
    {
        /* @var Question $question */
        foreach ($this->questionnaire->getQuestions() as $question) {

            if ($question->getDisabled()) {
                continue;
            }

            foreach ($this->request->request->all()['questionnaire']['question'] as $keyR => $valR) {
                $tmp = explode('-', $keyR);
                if ($tmp[1] == $question->getId()) {

                    if (! is_array($valR)) {
                        $strValeur = $valR;
                    } else {
                        $strValeur = '';
                        foreach ($valR as $v) {
                            $strValeur .= $v . '|';
                        }
                        $strValeur = substr($strValeur, 0, - 1);
                    }

                    $reponse = $this->return[$question->getid()]->reponse;
                    $reponse->setValeur($strValeur);
                    $reponse->setQuestion($question);

                    $this->return[$question->getid()]->reponse = $reponse;
                }
            }
        }
    }

    /**
     * Autorisation d'accès à un questionnaire donné
     * ex : si questionnaire déjà répondu, l'utilisateur n'y a plus accès
     * @param Questionnaire $questionnaire
     * @param Membre $membre
     */
    public function allowAccess(Questionnaire $questionnaire, Membre $membre)
    {
        if (! $questionnaire->getPublication() || $questionnaire->getDisabled()) {
            throw new AccessDeniedException("Ce questionnaire n'existe pas.");
        }

        $today = new \DateTime();
        if ($today->getTimestamp() > $questionnaire->getDateFin()->getTimestamp()) {
            throw new AccessDeniedException("La date limite de réponse du questionnaire est dépassée.");
        }

        $questionnaireRepo = $this->doctrine->getRepository(Questionnaire::class);
        if ($questionnaireRepo->HasAnswered($questionnaire->getId(), $membre->getId())) {
            throw new AccessDeniedException("Vous avez déjà répondu à ce questionnaire.");
        }
    }

    /**
     * Possibilité de personnaliser la description d'un questionnaire 
     * @nom et @prenom mis en place pour changement automatique par ceux de l'utilisateur connecté
     * @param Questionnaire $questionnaire
     * @param Membre $membre
     * @return \App\Entity\Questionnaire
     */
    public function formatDescription(Questionnaire $questionnaire, Membre $membre)
    {
        $masque = array(
            '@prenom' => $membre->getPrenom(),
            '@nom' => $membre->getNom()
        );
        
        $fields = array('Description', 'DescriptionAfterSubmit');
        
        foreach($fields as $field)
        {
            $txt = $questionnaire->{'get' . $field}();
            $txt = str_replace("@prenom", $masque['@prenom'], $txt);
            $txt = str_replace("@nom", $masque['@nom'], $txt);
            $questionnaire->{'set' . $field}($txt);
        }
        
        return $questionnaire;
    }
    
    /**
     *
     * Enregistrement des données
     * @param Membre $membre
     */
    private function saveData(Membre $membre)
    {
        $em = $this->doctrine->getManager();
        
        /* @var Reponse $reponse */
        foreach($this->return as $value)
        {
            $reponse = $value->reponse;
            $reponse->setMembre($membre);
            $em->persist($reponse);
        }
        $em->flush();
    }
}