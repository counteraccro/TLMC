<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry as Doctrine;
use App\Entity\Questionnaire;
use App\Entity\Reponse;
use App\Entity\Question;

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
    public function manage(Questionnaire $questionnaire)
    {
        $this->questionnaire = $questionnaire;
        $this->initialize();
        $this->createReponse();
        
        return $this->return;
    }

    /**
     * 
     */
    private function initialize()
    {
        foreach ($this->questionnaire->getQuestions() as $question) {
            $array = [
                'question' => $question,
                'reponse' => '',
                'erreur' => ''
            ];
            $this->return[$question->getid()] = (object) $array;
        }
    }
    
    /**
     * 
     */
    private function validate()
    {
        
    }

    /**
     * 
     */
    private function createReponse()
    {
        /* @var Question $question */
        foreach ($this->questionnaire->getQuestions() as $question) {
            
            if($question->getDisabled())
            {
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

                    $reponse = new Reponse();
                    $reponse->setValeur($strValeur);
                    //$reponse->setQuestion($question);
                    
                    $this->return[$question->getid()]->reponse = $reponse;
                }
            }
        }
    }
}