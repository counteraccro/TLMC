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
                'setStatut' => '1',
                'setDate' => '2017-04-12 12:00:00'
            ],
            'Participant-2' => [
                'setEvenement' => 'Evenement-2',
                'setFamille' => 'Famille-2',
                'setPatient' => 'Patient-2',
                'setStatut' => '0',
                'setDate' => '2018-04-12 12:00:00'
            ],
            'Participant-3' => [
                'setEvenement' => 'Evenement-1',
                'setPatient' => 'Patient-3',
                'setStatut' => '1',
                'setDate' => '2018-04-12 12:00:00'
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