<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Participant;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Evenement;
use App\Entity\Famille;
use App\Entity\Patient;

class LoadParticipantData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabParticipants = [
        'Participant' => [
            'Participant-1' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-1',
                'setPatient' => 'Patient-1',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-2' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-2',
                'setPatient' => 'Patient-2',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-3' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-5',
                'setPatient' => 'Patient-2',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-4' => [
                'setEvenement' => 'Evenement-2',
                'setFamille' => 'Famille-3',
                'setPatient' => 'Patient-3',
                'setStatut' => 1,
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Participant-5' => [
                'setEvenement' => 'Evenement-2',
                'setFamille' => 'Famille-4',
                'setPatient' => 'Patient-3',
                'setStatut' => 2,
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Participant-6' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-6',
                'setPatient' => 'Patient-7',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-7' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-7',
                'setPatient' => 'Patient-4',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-8' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-8',
                'setPatient' => 'Patient-4',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-9' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-9',
                'setPatient' => 'Patient-4',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-10' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-10',
                'setPatient' => 'Patient-4',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-11' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-11',
                'setPatient' => 'Patient-4',
                'setStatut' => 2,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-12' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-12',
                'setPatient' => 'Patient-4',
                'setStatut' => 2,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-13' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-14',
                'setPatient' => 'Patient-5',
                'setStatut' => 2,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-14' => [
                'setEvenement' => 'Evenement-5',
                'setFamille' => 'Famille-1',
                'setPatient' => 'Patient-1',
                'setStatut' => 2,
                'setDate' => '2018-03-14 19:00:00'
            ],
            'Participant-15' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-16',
                'setPatient' => 'Patient-6',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Participant-16' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-17',
                'setPatient' => 'Patient-6',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Participant-17' => [
                'setEvenement' => 'Evenement-2',
                'setFamille' => 'Famille-19',
                'setPatient' => 'Patient-8',
                'setStatut' => 1,
                'setDate' => '2018-10-30 18:00:00'
            ],
            'Participant-18' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-20',
                'setPatient' => 'Patient-9',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Participant-19' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-21',
                'setPatient' => 'Patient-10',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-20' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-22',
                'setPatient' => 'Patient-10',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-21' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-23',
                'setPatient' => 'Patient-10',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-22' => [
                'setEvenement' => 'Evenement-1',
                'setFamille' => 'Famille-24',
                'setPatient' => 'Patient-10',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'Participant-23' => [
                'setEvenement' => 'Evenement-4',
                'setFamille' => 'Famille-25',
                'setPatient' => 'Patient-11',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'Participant-24' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-26',
                'setPatient' => 'Patient-12',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Participant-25' => [
                'setEvenement' => 'Evenement-3',
                'setFamille' => 'Famille-27',
                'setPatient' => 'Patient-12',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'Participant-26' => [
                'setEvenement' => 'Evenement-5',
                'setFamille' => 'Famille-28',
                'setPatient' => 'Patient-13',
                'setStatut' => 1,
                'setDate' => '2018-03-14 19:00:00'
            ],
            'Participant-27' => [
                'setEvenement' => 'Evenement-5',
                'setFamille' => 'Famille-29',
                'setPatient' => 'Patient-13',
                'setStatut' => 1,
                'setDate' => '2018-03-14 19:00:00'
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $participantsArray = $this->container->getParameter('Participant');
        $participantsArray = $this->tabParticipants['Participant'];

        foreach ($participantsArray as $name => $object) {
            $participant = new Participant();
            $evenement = new Evenement();
            $famille = new Famille();
            $patient = new Patient();

            foreach ($object as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }
                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $participant->{$key}($evenement);
                } elseif ($key == 'setPatient') {
                    $patient = $this->getReference($val);
                    $participant->{$key}($patient);
                } elseif ($key == 'setFamille') {
                    $famille = $this->getReference($val);
                    $participant->{$key}($famille);
                } else {
                    $participant->{$key}($val);
                }
            }

            $manager->persist($participant);
            $this->addReference($name, $participant);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadEvenementData::class,
            LoadFamilleData::class,
            LoadPatientData::class
        );
    }
}