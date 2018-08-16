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
                'setDescription' => 'Bonjour @prenom @nom, merci de prendre quelques minutes pour répondre à ce questionnaire qui est une évaluation de satisfaction.',
                'setDateCreation' => '2018-12-25 17:00:00',
                'setDateFin' => '2018-12-30 17:00:00',
                'setJourRelance' => '2',
                'setDisabled' => '1',
                'setSlug' => 'soiree-annuelle',
                'setPublication' => '1',
                'setDatePublication' => '2018-12-26 17:00:00'
            ],
            'Questionnaire-2' => [
                'setTitre' => 'Formulaire d\'inscription',
                'setDescription' => 'Bonjour @prenom @nom, ce questionnaire permet d\'inscrire un nouveau participant.',
                'setDateCreation' => '2018-08-01 17:00:00',
                'setDateFin' => '2019-01-30 17:00:00',
                'setJourRelance' => '5',
                'setDisabled' => '0',
                'setSlug' => 'formulaire-d-inscription',
                'setPublication' => '0'
            ],
            'Questionnaire-3' => [
                'setTitre' => 'Enquête sur les bonbons',
                'setDescription' => 'Bonjour @prenom @nom, ce questionnaire va nous permettre de mieux connaître vos goûts. Merci d\'y répondre de façon honnête !',
                'setDateCreation' => '2018-08-02 19:00:00',
                'setDateFin' => '2018-09-03 23:00:00',
                'setJourRelance' => '5',
                'setDisabled' => '0',
                'setSlug' => 'enquete-sur-les-bonbons',
                'setPublication' => '1',
                'setDatePublication' => '2018-09-01 17:00:00'
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
                
                if($key == 'setDateCreation' || $key == 'setDateFin' || $key == 'setDatePublication')
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