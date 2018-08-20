<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Reponse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadReponseData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabReponses = [
         'Reponse' => [
            'Reponse-1' => [
                'setQuestion' => 'Question-1',
                'setMembre' => 'Membre-1',
                'setValeur' => '0',
                'setDate' => '2017-04-12 12:00:00',
                'setDisabled' => '1'
            ],
            'Reponse-2' => [
                'setQuestion' => 'Question-2',
                'setMembre' => 'Membre-1',
                'setValeur' => '',
                'setDate' => '2017-05-12 12:00:00',
                'setDisabled' => '0'
            ],
            'Reponse-3' => [
                'setQuestion' => 'Question-3',
                'setMembre' => 'Membre-2',
                'setValeur' => 'Voici la réponse à Q3',
                'setDate' => '2017-06-12 12:00:00',
                'setDisabled' => '1'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $reponsesArray = $this->container->getParameter('Reponse');
        $reponsesArray = $this->tabReponses['Reponse'];

        foreach ($reponsesArray as $name => $object) {
            $reponse = new Reponse();

            foreach ($object as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }
                if ($key == 'setQuestion') {
                    $val = $this->getReference($val);
                } elseif ($key == 'setMembre') {
                    $val = $this->getReference($val);
                }

                $reponse->{$key}($val);
            }

            $manager->persist($reponse);
            $this->addReference($name, $reponse);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadQuestionData::class,
            LoadMembreData::class
        );
    }
}