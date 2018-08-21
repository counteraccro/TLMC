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
                'setDate' => '2017-04-12 12:02:00',
                'setDisabled' => '1'
            ],
            'Reponse-2' => [
                'setQuestion' => 'Question-2',
                'setMembre' => 'Membre-1',
                'setValeur' => '',
                'setDate' => '2017-05-12 12:02:00',
                'setDisabled' => '0'
            ],
            'Reponse-3' => [
                'setQuestion' => 'Question-3',
                'setMembre' => 'Membre-2',
                'setValeur' => 'Voici la réponse à Q3',
                'setDate' => '2017-06-12 12:01:00',
                'setDisabled' => '1'
            ],
            'Reponse-4' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-18',
                'setValeur' => '1',
                'setDate' => '2018-08-21 17:10:00',
                'setDisabled' => '0'
            ],
            'Reponse-5' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-18',
                'setValeur' => '4',
                'setDate' => '2018-08-21 17:10:00',
                'setDisabled' => '0'
            ],
            'Reponse-6' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-18',
                'setValeur' => 'Je trouve que nous manquons de moyens pour certains évènements.',
                'setDate' => '2018-08-21 17:10:00',
                'setDisabled' => '0'
            ],
            'Reponse-7' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-18',
                'setValeur' => '0',
                'setDate' => '2018-08-21 17:10:00',
                'setDisabled' => '0'
            ],
            'Reponse-8' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-10',
                'setValeur' => '2',
                'setDate' => '2018-08-20 17:58:00',
                'setDisabled' => '0'
            ],
            'Reponse-9' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-10',
                'setValeur' => '1',
                'setDate' => '2018-08-20 17:58:00',
                'setDisabled' => '0'
            ],
            'Reponse-10' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-10',
                'setValeur' => 'Je suis assez satisfaite de l\'organisation générale de l\'association.',
                'setDate' => '2018-08-20 17:58:00',
                'setDisabled' => '0'
            ],
            'Reponse-11' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-10',
                'setValeur' => '1',
                'setDate' => '2018-08-20 17:58:00',
                'setDisabled' => '0'
            ],
            'Reponse-12' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-20',
                'setValeur' => '4',
                'setDate' => '2018-08-19 14:30:58',
                'setDisabled' => '0'
            ],
            'Reponse-13' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-20',
                'setValeur' => '0',
                'setDate' => '2018-08-19 14:30:58',
                'setDisabled' => '0'
            ],
            'Reponse-14' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-20',
                'setValeur' => 'Je pourrais parler durant des heures de ce sujet.',
                'setDate' => '2018-08-19 14:30:58',
                'setDisabled' => '0'
            ],
            'Reponse-15' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-20',
                'setValeur' => '2',
                'setDate' => '2018-08-19 14:30:58',
                'setDisabled' => '0'
            ],
            'Reponse-16' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-24',
                'setValeur' => '1',
                'setDate' => '2018-08-18 19:00:04',
                'setDisabled' => '0'
            ],
            'Reponse-17' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-24',
                'setValeur' => '4',
                'setDate' => '2018-08-18 19:00:04',
                'setDisabled' => '0'
            ],
            'Reponse-18' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-24',
                'setValeur' => 'Certaines prises électriques du bâtiment A sont défectueuses.',
                'setDate' => '2018-08-18 19:00:04',
                'setDisabled' => '0'
            ],
            'Reponse-19' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-24',
                'setValeur' => '3',
                'setDate' => '2018-08-18 19:00:04',
                'setDisabled' => '0'
            ],
            'Reponse-20' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-17',
                'setValeur' => '3',
                'setDate' => '2018-07-18 03:15:28',
                'setDisabled' => '0'
            ],
            'Reponse-21' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-17',
                'setValeur' => '2',
                'setDate' => '2018-07-18 03:15:28',
                'setDisabled' => '0'
            ],
            'Reponse-22' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-17',
                'setValeur' => 'Certaines chaises du bâtiment B sont défectueuses.',
                'setDate' => '2018-07-18 03:15:28',
                'setDisabled' => '0'
            ],
            'Reponse-23' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-17',
                'setValeur' => '4',
                'setDate' => '2018-07-18 03:15:28',
                'setDisabled' => '0'
            ],
            'Reponse-24' => [
                'setQuestion' => 'Question-9',
                'setMembre' => 'Membre-21',
                'setValeur' => '0',
                'setDate' => '2018-08-15 17:39:00',
                'setDisabled' => '0'
            ],
            'Reponse-25' => [
                'setQuestion' => 'Question-10',
                'setMembre' => 'Membre-21',
                'setValeur' => '1',
                'setDate' => '2018-08-15 17:39:00',
                'setDisabled' => '0'
            ],
            'Reponse-26' => [
                'setQuestion' => 'Question-11',
                'setMembre' => 'Membre-21',
                'setValeur' => 'Nous avons reçu toutes les fournitures demandées par notre service la semaine passée !',
                'setDate' => '2018-08-15 17:39:00',
                'setDisabled' => '0'
            ],
            'Reponse-27' => [
                'setQuestion' => 'Question-12',
                'setMembre' => 'Membre-21',
                'setValeur' => '5',
                'setDate' => '2018-08-15 17:39:00',
                'setDisabled' => '0'
            ],
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