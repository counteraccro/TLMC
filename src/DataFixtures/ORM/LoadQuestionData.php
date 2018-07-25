<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadQuestionData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabQuestions = [
        'Question' => [
            'Question-1' => [
                'setQuestionnaire' => 'Questionnaire-1',
                'setLibelle' => 'Avez-vous apprécié l\'ambiance générale de la soirée ?',
                'setLibelleTop' => 'Ambiance générale',
                'setLibelleBottom' => 'Bien-être/confort',
                'setType' => 'input',
                'setValeurDefaut' => 'Q1 val def',
                'setListeValeur' => '{ liste val en json }',
                'setDisabled' => '1',
                'setObligatoire' => 'true',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'message erreur Q1',
                'setOrdre' => '1'
            ],
            'Question-2' => [
                //'setQuestion' => 'Question-1',
                'setQuestionnaire' => 'Questionnaire-1',
                'setLibelle' => 'A quelle activité avez-vous participé durant la soirée ?',
                'setLibelleTop' => 'Activités proposées',
                'setLibelleBottom' => 'Activités/loisirs',
                'setType' => 'textarea',
                'setValeurDefaut' => 'Q2 val def',
                'setListeValeur' => '{ liste val en json }',
                'setDisabled' => '1',
                'setObligatoire' => 'false',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'message erreur Q2',
                'setOrdre' => '2'
            ],
            'Question-3' => [
                'setQuestionnaire' => 'Questionnaire-2',
                'setLibelle' => 'Libelle Q3',
                'setLibelleTop' => 'Libelle top Q3',
                'setLibelleBottom' => 'Libelle bottom Q3',
                'setType' => 'input',
                'setValeurDefaut' => 'Q3 val def',
                'setListeValeur' => '{ liste val en json }',
                'setDisabled' => '0',
                'setObligatoire' => 'true',
                'setRegles' => 'regles json',
                'setMessageErreur' => 'message erreur Q3',
                'setOrdre' => '1'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $questionsArray = $this->container->getParameter('Question');
        $questionsArray = $this->tabQuestions['Question'];

        foreach ($questionsArray as $name => $object) {
            $question = new Question();

            foreach ($object as $key => $val) {
                
                switch ($key) {
                    case 'setQuestionnaire':
                    case 'setQuestion':
                        $val = $this->getReference($val);
                        break;
                }
                
                $question->{$key}($val);
            }

            $manager->persist($question);
            $this->addReference($name, $question);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadQuestionnaireData::class
        );
    }
}