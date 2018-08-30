<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\SpecialiteEvenement;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Specialite;
use App\Entity\Evenement;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadSpecialiteEvenementData extends Fixture implements DependentFixtureInterface
{

    private $tabSpecialiteEvenement = [
        'SpecialiteEvenement' => [
            'SpecialiteEvenement-1' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-1',
                'setStatut' => 0,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'SpecialiteEvenement-2' => [
                'setEvenement' => 'Evenement-2',
                'setSpecialite' => 'Specialite-1',
                'setStatut' => 0,
                'setDate' => '2018-10-30 18:00:00'
            ],
            'SpecialiteEvenement-3' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-3',
                'setStatut' => 0,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'SpecialiteEvenement-4' => [
                'setEvenement' => 'Evenement-4',
                'setSpecialite' => 'Specialite-3',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'SpecialiteEvenement-5' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-4',
                'setStatut' => 0,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'SpecialiteEvenement-6' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-1',
                'setStatut' => 0,
                'setDate' => '2018-04-28 19:00:00'
            ],
            'SpecialiteEvenement-7' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-3',
                'setStatut' => 0,
                'setDate' => '2018-04-28 19:00:00'
            ],
            'SpecialiteEvenement-8' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-5',
                'setStatut' => 0,
                'setDate' => '2018-04-28 19:00:00'
            ],
            'SpecialiteEvenement-7' => [
                'setEvenement' => 'Evenement-5',
                'setSpecialite' => 'Specialite-4',
                'setStatut' => 0,
                'setDate' => '2018-04-28 19:00:00'
            ],
            'SpecialiteEvenement-8' => [
                'setEvenement' => 'Evenement-4',
                'setSpecialite' => 'Specialite-2',
                'setStatut' => 1,
                'setDate' => '2018-10-31 18:00:00'
            ],
            'SpecialiteEvenement-9' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-4',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'SpecialiteEvenement-9' => [
                'setEvenement' => 'Evenement-1',
                'setSpecialite' => 'Specialite-5',
                'setStatut' => 1,
                'setDate' => '2017-12-25 12:00:00'
            ],
            'SpecialiteEvenement-10' => [
                'setEvenement' => 'Evenement-2',
                'setSpecialite' => 'Specialite-6',
                'setStatut' => 1,
                'setDate' => '2018-10-30 18:00:00'
            ],
            'SpecialiteEvenement-11' => [
                'setEvenement' => 'Evenement-3',
                'setSpecialite' => 'Specialite-6',
                'setStatut' => 1,
                'setDate' => '2018-09-22 08:30:00'
            ],
            'SpecialiteEvenement-12' => [
                'setEvenement' => 'Evenement-6',
                'setSpecialite' => 'Specialite-7',
                'setStatut' => 2,
                'setDate' => '2016-07-14 18:00:00'
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
        $specialiteEvenementArray = $this->tabSpecialiteEvenement['SpecialiteEvenement'];
        foreach ($specialiteEvenementArray as $name => $value) {
            $specialiteEvenement = new SpecialiteEvenement();
            $specialite = new Specialite();
            $evenement = new Evenement();

            foreach ($value as $key => $val) {
                if ($key == 'setDate') {
                    $val = new \DateTime($val);
                }

                if ($key == 'setEvenement') {
                    $evenement = $this->getReference($val);
                    $specialiteEvenement->{$key}($evenement);
                } elseif ($key == 'setSpecialite') {
                    $specialite = $this->getReference($val);
                    $specialiteEvenement->{$key}($specialite);
                } else {
                    $specialiteEvenement->{$key}($val);
                }
            }
            $manager->persist($specialiteEvenement);
            $this->addReference($name, $specialiteEvenement);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadSpecialiteData::class,
            LoadEvenementData::class
        );
    }
}