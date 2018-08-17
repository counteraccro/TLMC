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
                'setDisabled' => true
            ],
            'Patient-4' => [
                'setNom' => 'Pierre',
                'setPrenom' => 'Henri',
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