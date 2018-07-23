<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\EvenementQuestionnaire;
use App\Entity\Questionnaire;
use App\Entity\Evenement;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadEvenementQuestionnaireData extends Fixture implements DependentFixtureInterface
{

    private $tabEvenementQuestionnaire = [
        'EvenementQuestionnaire' => [
            'EvenementQuestionnaire-1' => [
                'setEvenement' => 'Evenement-1',
                'setQuestionnaire' => 'Questionnaire-1',
                'setDate' => '2018-07-16'
            ],
            'EvenementQuestionnaire-2' => [
                'setEvenement' => 'Evenement-2',
                'setQuestionnaire' => 'Questionnaire-2',
                'setDate' => '2018-08-16'
            ]
        ]
    ];

    /*
     * public function __construct(ContainerInterface $container = null)
     * {
     * $this->container = $container;
     * }
     */
    public function load(ObjectManager $manager)
    {
        $EvenementQuestionnaireArray = $this->tabEvenementQuestionnaire['EvenementQuestionnaire'];
        foreach ($EvenementQuestionnaireArray as $name => $value) {
            $EvenementQuestionnaire = new EvenementQuestionnaire();
            $questionnaire = new Questionnaire();
            $evenement = new Evenement();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $EvenementQuestionnaire->{$key}($evenement);
                } elseif ($key == 'setQuestionnaire') {
                    $questionnaire = $this->getReference($val);
                    $EvenementQuestionnaire->{$key}($questionnaire);
                } else {
                    $EvenementQuestionnaire->{$key}($val);
                }
            }
            $manager->persist($EvenementQuestionnaire);
            $this->addReference($name, $EvenementQuestionnaire);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadEvenementData::class,
            LoadQuestionnaireData::class
        );
    }
}