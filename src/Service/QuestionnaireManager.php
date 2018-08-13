<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry as Doctrine;
use App\Entity\Questionnaire;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Controller\AppController;

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
     * Tableau de retour contenant une liste d'objet contenant
     * stdClass->reponse => object reponse
     * stdClass->question => object question
     * stdClass->erreur => object stdClass contenant
     * * *stdClass->isValide => true|false
     * * *stdClass->label => texte de l'erreur
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
     *
     * @param Questionnaire $questionnaire
     */
    public function manage(Questionnaire $questionnaire, $statut = AppController::PROD)
    {
        $this->questionnaire = $questionnaire;
        $this->initialize();
        $this->createReponse();
        $this->validate();

        return $this->return;
    }

    /**
     */
    private function initialize()
    {
        foreach ($this->questionnaire->getQuestions() as $question) {
            $array = [
                'question' => $question,
                'reponse' => new Reponse(),
                'erreur' => (object) array(
                    'is' => false,
                    'libelle' => ''
                )
            ];
            $this->return[$question->getid()] = (object) $array;
        }
    }

    /**
     */
    private function validate()
    {
        foreach ($this->return as $key => $object) {
            /* @var Question $question */
            $question = $object->question;

            /* @var Reponse $reponse */
            $reponse = $object->reponse;

            if ($question->getObligatoire()) {
                if (empty($reponse->getValeur())) {

                    $this->return[$key]->erreur->is = true;
                    $this->return[$key]->erreur->libelle = 'Cette question est obligatoire';
                    continue;
                }
            }

            
            if (in_array($question->getType(), array(
                AppController::TEXTAREATYPE,
                AppController::TEXTYPE
            ))) {
                if (AppController::PREG_MATCH_DATE == $question->getRegles()) {
                    if (! preg_match('/' . $question->getRegles() . '/', $reponse->getValeur())) {
                        $this->return[$key]->erreur->is = true;
                        $this->return[$key]->erreur->libelle = $question->getMessageErreur();
                        continue;
                    }
                } else {
                    if (preg_match('/' . $question->getRegles() . '/', $reponse->getValeur())) {
                        $this->return[$key]->erreur->is = true;
                        $this->return[$key]->erreur->libelle = $question->getMessageErreur();
                        continue;
                    }
                }
            }
        }
    }

    /**
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
}