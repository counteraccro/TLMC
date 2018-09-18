<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\FamillePatient;

class LoadFamillePatientData extends Fixture implements DependentFixtureInterface
{

    private $tabFamillePatient = [
        'FamillePatient' => [
            'FamillePatient-1' => [
                'setFamille' => 'Famille-1',
                'setPatient' => 'Patient-1',
                'setLienParente' => 1
            ],
            'FamillePatient-2' => [
                'setFamille' => 'Famille-2',
                'setPatient' => 'Patient-2',
                'setLienParente' => 2
            ],
            'FamillePatient-3' => [
                'setFamille' => 'Famille-3',
                'setPatient' => 'Patient-3',
                'setLienParente' => 3
            ],
            'FamillePatient-4' => [
                'setFamille' => 'Famille-4',
                'setPatient' => 'Patient-3',
                'setLienParente' => 2
            ],
            'FamillePatient-5' => [
                'setFamille' => 'Famille-5',
                'setPatient' => 'Patient-2',
                'setLienParente' => 4
            ],
            'FamillePatient-6' => [
                'setFamille' => 'Famille-6',
                'setPatient' => 'Patient-7',
                'setLienParente' => 4
            ],
            'FamillePatient-7' => [
                'setFamille' => 'Famille-7',
                'setPatient' => 'Patient-4',
                'setLienParente' => 9
            ],
            'FamillePatient-8' => [
                'setFamille' => 'Famille-8',
                'setPatient' => 'Patient-4',
                'setLienParente' => 10
            ],
            'FamillePatient-9' => [
                'setFamille' => 'Famille-9',
                'setPatient' => 'Patient-4',
                'setLienParente' => 4
            ],
            'FamillePatient-10' => [
                'setFamille' => 'Famille-10',
                'setPatient' => 'Patient-4',
                'setLienParente' => 2
            ],
            'FamillePatient-11' => [
                'setFamille' => 'Famille-11',
                'setPatient' => 'Patient-4',
                'setLienParente' => 1
            ],
            'FamillePatient-12' => [
                'setFamille' => 'Famille-12',
                'setPatient' => 'Patient-4',
                'setLienParente' => 10
            ],
            'FamillePatient-13' => [
                'setFamille' => 'Famille-13',
                'setPatient' => 'Patient-4',
                'setLienParente' => 10
            ],
            'FamillePatient-14' => [
                'setFamille' => 'Famille-14',
                'setPatient' => 'Patient-5',
                'setLienParente' => 9
            ],
            'FamillePatient-15' => [
                'setFamille' => 'Famille-15',
                'setPatient' => 'Patient-6',
                'setLienParente' => 1
            ],
            'FamillePatient-16' => [
                'setFamille' => 'Famille-16',
                'setPatient' => 'Patient-6',
                'setLienParente' => 2
            ],
            'FamillePatient-17' => [
                'setFamille' => 'Famille-17',
                'setPatient' => 'Patient-6',
                'setLienParente' => 3
            ],
            'FamillePatient-18' => [
                'setFamille' => 'Famille-18',
                'setPatient' => 'Patient-8',
                'setLienParente' => 2
            ],
            'FamillePatient-19' => [
                'setFamille' => 'Famille-19',
                'setPatient' => 'Patient-8',
                'setLienParente' => 4
            ],
            'FamillePatient-20' => [
                'setFamille' => 'Famille-20',
                'setPatient' => 'Patient-9',
                'setLienParente' => 4
            ],
            'FamillePatient-21' => [
                'setFamille' => 'Famille-21',
                'setPatient' => 'Patient-10',
                'setLienParente' => 10
            ],
            'FamillePatient-22' => [
                'setFamille' => 'Famille-22',
                'setPatient' => 'Patient-10',
                'setLienParente' => 10
            ],
            'FamillePatient-23' => [
                'setFamille' => 'Famille-23',
                'setPatient' => 'Patient-10',
                'setLienParente' => 13
            ],
            'FamillePatient-24' => [
                'setFamille' => 'Famille-24',
                'setPatient' => 'Patient-10',
                'setLienParente' => 13
            ],
            'FamillePatient-25' => [
                'setFamille' => 'Famille-25',
                'setPatient' => 'Patient-11',
                'setLienParente' => 3
            ],
            'FamillePatient-26' => [
                'setFamille' => 'Famille-26',
                'setPatient' => 'Patient-12',
                'setLienParente' => 10
            ],
            'FamillePatient-27' => [
                'setFamille' => 'Famille-27',
                'setPatient' => 'Patient-12',
                'setLienParente' => 9
            ],
            'FamillePatient-28' => [
                'setFamille' => 'Famille-28',
                'setPatient' => 'Patient-13',
                'setLienParente' => 4
            ],
            'FamillePatient-29' => [
                'setFamille' => 'Famille-29',
                'setPatient' => 'Patient-13',
                'setLienParente' => 3
            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $famillePatientArray = $this->tabFamillePatient['FamillePatient'];
        foreach ($famillePatientArray as $name => $value) {
            $famillePatient = new FamillePatient();

            foreach ($value as $key => $val) {
                if($key == 'setFamille' || $key == 'setPatient'){
                    $val = $this->getReference($val);
                }
                $famillePatient->{$key}($val);
            }

            $manager->persist($famillePatient);
            $this->addReference($name, $famillePatient);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadFamilleData::class,
            LoadPatientData::class
        );
    }
}
    
