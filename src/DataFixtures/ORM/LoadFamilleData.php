<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Famille;
use App\Entity\FamilleAdresse;
use App\Entity\Patient;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadFamilleData extends Fixture implements DependentFixtureInterface
{

    private $tabFamille = [
        'Famille' => [
            'Famille-1' => [
                'setNom' => 'Dupont',
                'setPrenom' => 'Jean-Henri',
                'setLienFamille' => '1',
                'setEmail' => 'jh.dupont@gmail.com',
                'setPmr' => '0',
                'setNumeroTel' => '0146598756',
                'setFamilleAdresse' => 'FamilleAdresse-1',
                'setPatient' => 'Patient-1',
                'setDisabled' => 1
            ],
            'Famille-2' => [
                'setNom' => 'Martin',
                'setPrenom' => 'Marie',
                'setLienFamille' => '2',
                'setEmail' => 'marie.martin.256@gmail.com',
                'setPmr' => '1',
                'setNumeroTel' => '0381454546',
                'setFamilleAdresse' => 'FamilleAdresse-2',
                'setPatient' => 'Patient-2',
                'setDisabled' => 0
            ],
            'Famille-3' => [
                'setNom' => 'Garcia',
                'setPrenom' => 'Sergio',
                'setLienFamille' => '3',
                'setEmail' => 's.garcia@gmail.com',
                'setPmr' => '0',
                'setNumeroTel' => '+33785924563',
                'setFamilleAdresse' => 'FamilleAdresse-3',
                'setPatient' => 'Patient-3',
                'setDisabled' => 1
            ],
            'Famille-4' => [
                'setNom' => 'Garcia',
                'setPrenom' => 'Sandra',
                'setLienFamille' => '2',
                'setEmail' => 's.garcia2@gmail.com',
                'setPmr' => '0',
                'setNumeroTel' => '+33685924563',
                'setFamilleAdresse' => 'FamilleAdresse-3',
                'setPatient' => 'Patient-3',
                'setDisabled' => 1
            ],
            'Famille-5' => [
                'setNom' => 'Sasoeur',
                'setPrenom' => 'Marlene',
                'setLienFamille' => '4',
                'setEmail' => 'm.sasoeur@gmail.com',
                'setPmr' => '0',
                'setNumeroTel' => '+33145566778',
                'setFamilleAdresse' => 'FamilleAdresse-4',
                'setPatient' => 'Patient-2',
                'setDisabled' => 1
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
        $familleArray = $this->tabFamille['Famille'];
        foreach ($familleArray as $name => $value) {
            $famille = new Famille();
            $familleAdresse = new FamilleAdresse();
            $patient = new Patient();

            foreach ($value as $key => $val) {
                if ($key == 'setFamilleAdresse') {
                    $familleAdresse = $this->getReference($val);
                    $famille->{$key}($familleAdresse);
                } elseif ($key == 'setPatient') {
                    $patient = $this->getReference($val);
                    $famille->{$key}($patient);
                } else {
                    $famille->{$key}($val);
                }
            }

            $manager->persist($famille);
            $this->addReference($name, $famille);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadFamilleAdresseData::class,
            LoadPatientData::class
        );
    }
}