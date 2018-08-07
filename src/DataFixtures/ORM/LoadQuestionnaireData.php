<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Questionnaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadQuestionnaireData extends Fixture
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabQuestionnaires = [
        'Questionnaire' => [
            'Questionnaire-1' => [
                'setTitre' => 'Soirée annuelle',
                'setDescription' => 'Ce questionnaire est une évaluation de satisfaction.',
                'setDateCreation' => '2017-12-25 17:00:00',
                'setDateFin' => '2017-12-30 17:00:00',
                'setJourRelance' => '2',
                'setDisabled' => '1',
                'setSlug' => 'soiree-annuelle'
            ],
            'Questionnaire-2' => [
                'setTitre' => 'Formulaire d\'inscription',
                'setDescription' => 'Ce questionnaire permet d\'inscrire un nouveau participant.',
                'setDateCreation' => '2018-08-01 17:00:00',
                'setDateFin' => '2019-01-30 17:00:00',
                'setJourRelance' => '5',
                'setDisabled' => '0',
                'setSlug' => 'formulaire-d-inscription'
            ],
            'Questionnaire-3' => [
                'setTitre' => 'Enquête sur les bonbons',
                'setDescription' => 'Ce questionnaire va nous permettre de mieux connaître vos goûts.',
                'setDateCreation' => '2018-08-02 19:00:00',
                'setDateFin' => '2018-09-03 23:00:00',
                'setJourRelance' => '5',
                'setDisabled' => '0',
                'setSlug' => 'enquete-sur-les-bonbons'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

		//$questionnairesArray = $this->container->getParameter('Questionnaire');
		$questionnairesArray = $this->tabQuestionnaires['Questionnaire'];
		
		foreach ($questionnairesArray as $name => $object) {
            $questionnaire = new Questionnaire();

            foreach ($object as $key => $val) {
                
                if($key == 'setDateCreation' || $key == 'setDateFin')
                {
                    $val = new \DateTime($val);
                }
                
                $questionnaire->{$key}($val);
            }

            $manager->persist($questionnaire);
            $this->addReference($name, $questionnaire);
        }
        $manager->flush();
    }
}