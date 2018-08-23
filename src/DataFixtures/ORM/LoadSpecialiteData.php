<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Etablissement;
use App\Entity\Specialite;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// use Symfony\Component\DependencyInjection\ContainerInterface;
class LoadSpecialiteData extends Fixture implements DependentFixtureInterface
{

    private $tabSpecialite = [
        'Specialite' => [
            'Specialite-1' => [
                'setCodeLogistique' => 'ENC_01',
                'setAdulte' => true,
                'setPediatrie' => false,
                'setService' => 'Oncologie',
                'setEtablissement' => 'Etablissement-1',
                'setDisabled' => 0
            ],
            'Specialite-2' => [
                'setCodeLogistique' => 'CHI_01',
                'setAdulte' => false,
                'setPediatrie' => true,
                'setService' => 'Chirurgie',
                'setEtablissement' => 'Etablissement-1',
                'setDisabled' => 0
            ],
            'Specialite-3' => [
                'setCodeLogistique' => 'CARD_01',
                'setAdulte' => true,
                'setPediatrie' => true,
                'setService' => 'Cardiologie',
                'setEtablissement' => 'Etablissement-1',
                'setDisabled' => 0
            ],
            'Specialite-4' => [
                'setCodeLogistique' => 'CARD_02',
                'setAdulte' => false,
                'setPediatrie' => true,
                'setService' => 'Cardiologie',
                'setEtablissement' => 'Etablissement-3',
                'setDisabled' => 0
            ],
            'Specialite-5' => [
                'setCodeLogistique' => 'PNEU_03',
                'setAdulte' => true,
                'setPediatrie' => true,
                'setService' => 'Pneumologie',
                'setEtablissement' => 'Etablissement-3',
                'setDisabled' => 0
            ],
            'Specialite-6' => [
                'setCodeLogistique' => 'ADD_01',
                'setAdulte' => true,
                'setPediatrie' => false,
                'setService' => 'Addictologie',
                'setEtablissement' => 'Etablissement-3',
                'setDisabled' => 1
            ],
            'Specialite-7' => [
                'setCodeLogistique' => 'ONC_01',
                'setAdulte' => true,
                'setPediatrie' => false,
                'setService' => 'Oncologie',
                'setEtablissement' => 'Etablissement-5',
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
        $specialiteArray = $this->tabSpecialite['Specialite'];
        foreach ($specialiteArray as $name => $value) {
            $specialite = new Specialite();
            $etablissement = new Etablissement();
          
            foreach ($value as $key => $val) {
                if ($key == 'setEtablissement') {
                    $etablissement = $this->getReference($val);
                    $specialite->{$key}($etablissement);
                } else {
                    $specialite->{$key}($val);
                }
            }
            $manager->persist($specialite);
            $this->addReference($name, $specialite);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadEtablissementData::class
        );
    }
}