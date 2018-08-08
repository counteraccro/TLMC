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
        $reponses = array();

        $this->pre($reponses);

        /* @var Question $question */
        foreach ($questionnaire->getQuestions() as $question) {
            foreach ($this->request->request->all()['questionnaire']['question'] as $keyR => $valR) {
                $tmp = explode('-', $keyR);
                if ($tmp[1] == $question->getId()) {
                    if (! is_array($valR)) {
                        $reponse = new Reponse();
                        $reponse->setValeur($valR);
                        $reponse->setQuestion($question);
                        $reponses[] = $reponse;
                    }
                }
            }
        }
        
        return $reponses;
    }
}