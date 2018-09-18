<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Famille;
use App\Entity\FamilleAdresse;
use App\Entity\Patient;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadFamilleData extends Fixture implements DependentFixtureInterface
{

    private $tabFamille = [
        'Famille' => [
            'Famille-1' => [
                'setNom' => 'Dupont',
                'setPrenom' => 'Jean-Henri',
                'setLienFamille' => 1,
                'setEmail' => 'jh.dupont@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0146598756',
                'setFamilleAdresse' => 'FamilleAdresse-1',
                'setPatient' => 'Patient-1',
                'setDisabled' => false
            ],
            'Famille-2' => [
                'setNom' => 'Martin',
                'setPrenom' => 'Marie',
                'setLienFamille' => 2,
                'setEmail' => 'marie.martin.256@gmail.com',
                'setPmr' => true,
                'setNumeroTel' => '0381454546',
                'setFamilleAdresse' => 'FamilleAdresse-2',
                'setPatient' => 'Patient-2',
                'setDisabled' => false
            ],
            'Famille-3' => [
                'setNom' => 'Garcia',
                'setPrenom' => 'Sergio',
                'setLienFamille' => 3,
                'setEmail' => 's.garcia@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33785924563',
                'setFamilleAdresse' => 'FamilleAdresse-3',
                'setPatient' => 'Patient-3',
                'setDisabled' => true
            ],
            'Famille-4' => [
                'setNom' => 'Garcia',
                'setPrenom' => 'Sandra',
                'setLienFamille' => 2,
                'setEmail' => 's.garcia2@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33685924563',
                'setFamilleAdresse' => 'FamilleAdresse-3',
                'setPatient' => 'Patient-3',
                'setDisabled' => true
            ],
            'Famille-5' => [
                'setNom' => 'Sasoeur',
                'setPrenom' => 'Marlene',
                'setLienFamille' => 4,
                'setEmail' => 'm.sasoeur@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33145566778',
                'setFamilleAdresse' => 'FamilleAdresse-4',
                'setPatient' => 'Patient-2',
                'setDisabled' => false
            ],
            'Famille-6' => [
                'setNom' => 'Skywalker',
                'setPrenom' => 'Leïa',
                'setLienFamille' => 4,
                'setEmail' => 'princesse.leia@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33145566778',
                'setFamilleAdresse' => 'FamilleAdresse-5',
                'setPatient' => 'Patient-7',
                'setDisabled' => false
            ],
            'Famille-7' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Mélanie',
                'setLienFamille' => 9,
                'setEmail' => 'melanie.henri@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0113754313',
                'setFamilleAdresse' => 'FamilleAdresse-6',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-8' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Daniel',
                'setLienFamille' => 10,
                'setEmail' => 'daniel.henri@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0113754313',
                'setFamilleAdresse' => 'FamilleAdresse-6',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-9' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Eva',
                'setLienFamille' => 4,
                'setEmail' => 'eva-henri@gmail.com',
                'setPmr' => true,
                'setNumeroTel' => '0113754313',
                'setFamilleAdresse' => 'FamilleAdresse-6',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-10' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Gertrude',
                'setLienFamille' => 2,
                'setEmail' => 'gertrude.henri@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33145564678',
                'setFamilleAdresse' => 'FamilleAdresse-7',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-11' => [
                'setNom' => 'Henri',
                'setPrenom' => 'Michel',
                'setLienFamille' => 1,
                'setEmail' => 'henri.michel@gmail.com',
                'setPmr' => true,
                'setNumeroTel' => '+33145564678',
                'setFamilleAdresse' => 'FamilleAdresse-7',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-12' => [
                'setNom' => 'Micoton',
                'setPrenom' => 'Mylène',
                'setLienFamille' => 10,
                'setEmail' => 'mylene.micoton@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0656545253',
                'setFamilleAdresse' => 'FamilleAdresse-8',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-13' => [
                'setNom' => 'De La Tour',
                'setPrenom' => 'Isabella',
                'setLienFamille' => 10,
                'setEmail' => 'isabella.henri@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0778797475',
                'setFamilleAdresse' => 'FamilleAdresse-9',
                'setPatient' => 'Patient-4',
                'setDisabled' => false
            ],
            'Famille-14' => [
                'setNom' => 'Joséphine',
                'setPrenom' => 'Irina',
                'setLienFamille' => 9,
                'setEmail' => 'irina.champs@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33178797475',
                'setFamilleAdresse' => 'FamilleAdresse-10',
                'setPatient' => 'Patient-5',
                'setDisabled' => false
            ],
            'Famille-15' => [
                'setNom' => 'Jean-Jean',
                'setPrenom' => 'Dimitri',
                'setLienFamille' => 1,
                'setEmail' => 'dimitri.jean.jean@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33121232425',
                'setFamilleAdresse' => 'FamilleAdresse-11',
                'setPatient' => 'Patient-6',
                'setDisabled' => false
            ],
            'Famille-16' => [
                'setNom' => 'Jean-Jean',
                'setPrenom' => 'Hélène',
                'setLienFamille' => 2,
                'setEmail' => 'helene.jean.jean@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '+33178797475',
                'setFamilleAdresse' => 'FamilleAdresse-11',
                'setPatient' => 'Patient-6',
                'setDisabled' => false
            ],
            'Famille-17' => [
                'setNom' => 'Jean-Jean',
                'setPrenom' => 'Thomas',
                'setLienFamille' => 3,
                'setEmail' => 'thomas.jean.jean@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0608845692',
                'setFamilleAdresse' => 'FamilleAdresse-12',
                'setPatient' => 'Patient-6',
                'setDisabled' => false
            ],
            'Famille-18' => [
                'setNom' => 'Santos',
                'setPrenom' => 'Elena',
                'setLienFamille' => 2,
                'setEmail' => 'elena.santos@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0669922554',
                'setFamilleAdresse' => 'FamilleAdresse-13',
                'setPatient' => 'Patient-8',
                'setDisabled' => false
            ],
            'Famille-19' => [
                'setNom' => 'Santos',
                'setPrenom' => 'Carlita',
                'setLienFamille' => 4,
                'setEmail' => 'elena.santos@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0669922554',
                'setFamilleAdresse' => 'FamilleAdresse-13',
                'setPatient' => 'Patient-8',
                'setDisabled' => false
            ],
            'Famille-20' => [
                'setNom' => 'Fontaine',
                'setPrenom' => 'Lydie',
                'setLienFamille' => 4,
                'setEmail' => 'lydie.fontaine@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0777787975',
                'setFamilleAdresse' => 'FamilleAdresse-14',
                'setPatient' => 'Patient-9',
                'setDisabled' => false
            ],
            'Famille-21' => [
                'setNom' => 'Raymond',
                'setPrenom' => 'Albert',
                'setLienFamille' => 10,
                'setEmail' => 'raymond.albert.65@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0122564789',
                'setFamilleAdresse' => 'FamilleAdresse-15',
                'setPatient' => 'Patient-10',
                'setDisabled' => false
            ],
            'Famille-22' => [
                'setNom' => 'Raymond',
                'setPrenom' => 'Sarah',
                'setLienFamille' => 10,
                'setEmail' => 'sarah.raymond@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0122564789',
                'setFamilleAdresse' => 'FamilleAdresse-15',
                'setPatient' => 'Patient-10',
                'setDisabled' => false
            ],
            'Famille-23' => [
                'setNom' => 'Raymond',
                'setPrenom' => 'Kelly',
                'setLienFamille' => 13,
                'setEmail' => 'sarah.raymond@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0122564789',
                'setFamilleAdresse' => 'FamilleAdresse-15',
                'setPatient' => 'Patient-10',
                'setDisabled' => false
            ],
            'Famille-24' => [
                'setNom' => 'Raymond',
                'setPrenom' => 'Kevin',
                'setLienFamille' => 13,
                'setEmail' => 'sarah.raymond@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0122564789',
                'setFamilleAdresse' => 'FamilleAdresse-15',
                'setPatient' => 'Patient-10',
                'setDisabled' => false
            ],
            'Famille-25' => [
                'setNom' => 'Lafeuille',
                'setPrenom' => 'Leni',
                'setLienFamille' => 3,
                'setEmail' => 'david.lafeuille@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0122564789',
                'setFamilleAdresse' => 'FamilleAdresse-16',
                'setPatient' => 'Patient-11',
                'setDisabled' => false
            ],
            'Famille-26' => [
                'setNom' => 'Duroc',
                'setPrenom' => 'Rachelle',
                'setLienFamille' => 10,
                'setEmail' => 'rachelle.duroc@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0133564789',
                'setFamilleAdresse' => 'FamilleAdresse-17',
                'setPatient' => 'Patient-12',
                'setDisabled' => false
            ],
            'Famille-27' => [
                'setNom' => 'Duroc',
                'setPrenom' => 'Maéva',
                'setLienFamille' => 9,
                'setEmail' => 'david.lafeuille@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0133564789',
                'setFamilleAdresse' => 'FamilleAdresse-17',
                'setPatient' => 'Patient-12',
                'setDisabled' => false
            ],
            'Famille-28' => [
                'setNom' => 'Lanoix',
                'setPrenom' => 'Christelle',
                'setLienFamille' => 4,
                'setEmail' => 'christelle.lanoix@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0146454243',
                'setFamilleAdresse' => 'FamilleAdresse-18',
                'setPatient' => 'Patient-13',
                'setDisabled' => false
            ],
            'Famille-29' => [
                'setNom' => 'Lanoix',
                'setPrenom' => 'Christian',
                'setLienFamille' => 3,
                'setEmail' => 'christian.lanoix@gmail.com',
                'setPmr' => false,
                'setNumeroTel' => '0146454243',
                'setFamilleAdresse' => 'FamilleAdresse-18',
                'setPatient' => 'Patient-13',
                'setDisabled' => false
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