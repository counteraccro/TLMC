<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TypeEvenement;

class LoadTypeEvenementData extends Fixture
{

    private $tabTypeEvenement = [
        'TypeEvenement' => [
            'TypeEvenement-1' => [
                'setNom' => 'Repas',
                'setDisabled' => 0
            ],
            'TypeEvenement-2' => [
                'setNom' => 'Sortie',
                'setDisabled' => 0
            ],
            'TypeEvenement-3' => [
                'setNom' => 'Spectacle',
                'setDisabled' => 0
            ],
            'TypeEvenement-4' => [
                'setNom' => 'Fête',
                'setDisabled' => 0
            ],
            'TypeEvenement-5' => [
                'setNom' => 'Animation',
                'setDisabled' => 0
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
        $typeEvenementArray = $this->tabTypeEvenement['TypeEvenement'];
        foreach ($typeEvenementArray as $name => $value) {
            $type_evenement = new TypeEvenement();

            foreach ($value as $key => $val) {
                $type_evenement->{$key}($val);
            }
            $manager->persist($type_evenement);
            $this->addReference($name, $type_evenement);
        }
        $manager->flush();
    }
}