<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Patient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Specialite;

class LoadPatientData extends Fixture implements DependentFixtureInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    private $tabPatient = [
        'Patient' => [
            'Patient-1' => [
                'setNom' => 'Oréo',
                'setPrenom' => 'Ingrid',
                'setDateNaissance' => '1991-09-30 11:29:00',
                'setPMR' => false,
                'setSpecialite' => 'Specialite-1',
                'setDisabled' => false
            ],
            'Patient-2' => [
                'setNom' => 'Lagarde',
                'setPrenom' => 'Christine',
                'setDateNaissance' => '1993-08-23 17:55:00',
                'setPMR' => false,
                'setSpecialite' => 'Specialite-1',
                'setDisabled' => false
            ],
            'Patient-3' => [
                'setNom' => 'LeChef',
                'setPrenom' => 'Aymeric',
                'setDateNaissance' => '1984-04-12 11:11:11',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-1',
                'setDisabled' => false
            ],
            'Patient-4' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Pierre',
                'setDateNaissance' => '1969-11-30 22:22:00',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-2',
                'setDisabled' => false
            ],
            'Patient-5' => [
                'setNom' => 'Joséphine',
                'setPrenom' => 'Charles-Antoine',
                'setDateNaissance' => '1989-06-14 15:12:00',
                'setPMR' => false,
                'setSpecialite' => 'Specialite-3',
                'setDisabled' => false
            ],
            'Patient-6' => [
                'setNom' => 'Jean-Jean',
                'setPrenom' => 'Charles-Henri',
                'setDateNaissance' => '2001-03-05 03:05:01',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-4',
                'setDisabled' => false
            ],
            'Patient-7' => [
                'setNom' => 'Skywalker',
                'setPrenom' => 'Luke',
                'setDateNaissance' => '1960-03-15 15:03:19',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-5',
                'setDisabled' => false
            ],
            'Patient-8' => [
                'setNom' => 'Santos',
                'setPrenom' => 'Carl',
                'setDateNaissance' => '2012-12-12 12:12:12',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-6',
                'setDisabled' => false
            ],
            'Patient-9' => [
                'setNom' => 'Fontaine',
                'setPrenom' => 'Marc',
                'setDateNaissance' => '1998-08-08 08:08:00',
                'setPMR' => false,
                'setSpecialite' => 'Specialite-6',
                'setDisabled' => false
            ],
            'Patient-10' => [
                'setNom' => 'Draveil',
                'setPrenom' => 'John',
                'setDateNaissance' => '1911-11-11 11:11:11',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-1',
                'setDisabled' => false
            ],
            'Patient-11' => [
                'setNom' => 'Lafeuille',
                'setPrenom' => 'Lisa',
                'setDateNaissance' => '2010-05-05 05:05:05',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-2',
                'setDisabled' => false
            ],
            'Patient-12' => [
                'setNom' => 'Duroc',
                'setPrenom' => 'Rémi',
                'setDateNaissance' => '1988-05-30 12:15:00',
                'setPMR' => false,
                'setSpecialite' => 'Specialite-3',
                'setDisabled' => false
            ],
            'Patient-13' => [
                'setNom' => 'Lanoix',
                'setPrenom' => 'Christophe',
                'setDateNaissance' => '2001-03-05 03:05:01',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-4',
                'setDisabled' => false
            ],
            'Patient-14' => [
                'setNom' => 'Tandem',
                'setPrenom' => 'Frédéric',
                'setDateNaissance' => '1960-01-14 14:01:00',
                'setPMR' => true,
                'setSpecialite' => 'Specialite-7',
                'setDisabled' => true
            ]
        ]
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // $patientsArray = $this->container->getParameter('Patient');
        $patientsArray = $this->tabPatient['Patient'];

        foreach ($patientsArray as $name => $object) {
            $patient = new Patient();
            $specialite = new Specialite();

            foreach ($object as $key => $val) {

                if ($key == 'setDateNaissance') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setSpecialite') {
                    $specialite = $this->getReference($val);
                    $patient->{$key}($specialite);
                } else {
                    $patient->{$key}($val);
                }
            }

            $manager->persist($patient);
            $this->addReference($name, $patient);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadSpecialiteData::class
        );
    }
}